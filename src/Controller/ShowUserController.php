<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\Customer;
use App\Service\UserService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowUserController extends AbstractController
{
    #[Route('/api/customer/{customer_id}/user/{id}', name: 'app_show_user', methods: ['GET'])]
    public function showUser(
        #[MapEntity(id: 'customer_id')] Customer $customer,
        #[MapEntity(id: 'id')] AppUser $user,
        UserService $userService,
    ): JsonResponse {
        $user = $userService->getUser($customer, $user);

        return new JsonResponse($user, Response::HTTP_OK, [], true);
    }
}
