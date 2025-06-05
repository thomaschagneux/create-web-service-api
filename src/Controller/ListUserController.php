<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Service\UserService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ListUserController extends AbstractController
{
    /**
     * @throws InvalidArgumentException
     */
    #[Route('/api/customer/{id}/user-list', name: 'list_user_by_customer')]
    public function listUsers(Customer $customer, Request $request, UserService $userService): JsonResponse
    {
        $page = $request->query->get('page', '1');
        $limit = $request->query->get('limit', '20');

        if (!ctype_digit($page) || !ctype_digit($limit)) {
            return new JsonResponse(['message' => 'Page or limit is not numeric'], 400);
        }

        $jsonUserList = $userService->getUser((int) $page, (int) $limit, $customer);

        return new JsonResponse($jsonUserList, 200, [], true);
    }
}
