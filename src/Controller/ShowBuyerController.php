<?php

namespace App\Controller;

use App\Entity\ApiUser;
use App\Entity\Buyer;
use App\Service\BuyerService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ShowBuyerController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer un utilisateur lié à un client.
     */
    #[Route('/api/buyer/{id}', name: 'show_buyer', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return a buyer',
        content: new OA\JsonContent(
            ref: new Model(type: ApiUser::class, groups: ['getBuyer'])
        )
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
    #[IsGranted('ROLE_ADMIN')]
    public function showBuyer(
        #[MapEntity(id: 'id')] Buyer $buyer,
        BuyerService $userService,
    ): JsonResponse {
        /** @var ApiUser $actualUser */
        $actualUser = $this->getUser();

        $buyerSerialized = $userService->getSerializedBuyer($buyer, $actualUser);

        return new JsonResponse($buyerSerialized, Response::HTTP_OK, [], true);
    }
}
