<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\Customer;
use App\Service\UserService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ShowUserController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer un utilisateur lié à un client.
     */
    #[Route('/api/customer/{customer_id}/user/{id}', name: 'show_user', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return a user',
        content: new OA\JsonContent(
            ref: new Model(type: AppUser::class, groups: ['getUser'])
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
    public function showUser(
        #[MapEntity(id: 'customer_id')] Customer $customer,
        #[MapEntity(id: 'id')] AppUser $user,
        UserService $userService,
    ): JsonResponse {
        $user = $userService->getUser($customer, $user);

        return new JsonResponse($user, Response::HTTP_OK, [], true);
    }
}
