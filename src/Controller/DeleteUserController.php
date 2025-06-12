<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteUserController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/api/customer/{customer_id}/user/{id}', name: 'delete_user', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'User deleted successfully'
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request - User and customer are not linked'
    )]
    #[OA\Parameter(
        name: 'customer_id',
        description: 'The customer ID',
        in: 'path',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The user ID',
        in: 'path',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag('Users')]
    public function deleteUser(
        #[MapEntity(id: 'customer_id')] Customer $customer,
        #[MapEntity(id: 'id')] AppUser $appUser,
    ): JsonResponse {
        if ($customer->getId() !== $appUser->getCustomer()->getId()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "l'utilisateur et le client ne sont pas liés");
        }
        $this->entityManager->remove($appUser);
        $this->entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
