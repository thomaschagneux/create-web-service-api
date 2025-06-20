<?php

namespace App\Controller;

use App\Entity\ApiUser;
use App\Entity\Buyer;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteUserController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Cette méthode permet de supprimer un acheteur lié à l'utilisateur actuel.
     */
    #[Route('/api/buyer/{id}', name: 'delete_buyer', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Buyer deleted successfully'
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request - You cannot delete this buyer'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The buyer ID',
        in: 'path',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag('Buyers')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(
        #[MapEntity(id: 'id')] Buyer $buyer,
    ): JsonResponse {
        /** @var ApiUser $actualUser */
        $actualUser = $this->getUser();
        $buyers = $actualUser->getBuyers();

        $buyerName = $buyer->getFirstName().' '.$buyer->getLastName();

        if (!$buyers->contains($buyer)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'This buyer is not linked to you');
        }

        $this->entityManager->remove($buyer);
        $this->entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT, ['x-deleted-buyer' => $buyerName]);
    }
}
