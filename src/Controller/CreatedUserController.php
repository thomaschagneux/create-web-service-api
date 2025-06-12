<?php

namespace App\Controller;

use App\Dto\UserData;
use App\Entity\Customer;
use App\Service\UserService;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CreatedUserController extends AbstractController
{
    #[Route('/api/customer/{id}/user', name: 'created_customer_user', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'User created successfully'
    )]
    #[OA\Response(
        response: 404,
        description: 'Customer not found'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The customer ID',
        in: 'path',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'User data to create',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: UserData::class)
        )
    )]
    #[OA\Tag('Users')]
    #[IsGranted('ROLE_ADMIN')]
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
