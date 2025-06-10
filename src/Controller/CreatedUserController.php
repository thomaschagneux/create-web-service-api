<?php

namespace App\Controller;

use App\Dto\UserData;
use App\Entity\Customer;
use App\Service\UserService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

final class CreatedUserController extends AbstractController
{
    #[Route('/api/customer/{id}/user', name: 'created_customer_user', methods: ['POST'])]
    public function createCustomerUser(Customer $customer, Request $request, UserService $userService, SerializerInterface $serializer): JsonResponse
    {
        /** @var UserData $userData */
        $userData = $serializer->deserialize($request->getContent(), UserData::class, 'json');

        if (null === $customer->getId()) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Customer not found');
        }

        $userData->setCustomerId($customer->getId());

        $userService->createUser($userData);

        return new JsonResponse(["L'utilisateur a bien été créé"], Response::HTTP_CREATED, []);
    }
}
