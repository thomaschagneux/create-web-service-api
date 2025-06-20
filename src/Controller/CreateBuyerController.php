<?php

namespace App\Controller;

use App\Dto\BuyerData;
use App\Entity\ApiUser;
use App\Service\BuyerService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CreateBuyerController extends AbstractController
{
    public function __construct(
    ) {
    }

    /**
     * Cette méthode permet de créer un acheteur lié à l'utilisateur actuel..
     */
    #[Route('/api/buyer', name: 'created_buyer', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Buyer created successfully'
    )]
    #[OA\Response(
        response: 404,
        description: 'ApiUser not found'
    )]
    #[OA\RequestBody(
        description: 'Buyer data to create',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: BuyerData::class)
        )
    )]
    #[OA\Tag('Buyers')]
    #[IsGranted('ROLE_ADMIN')]
    public function createCustomerBuyer(
        BuyerService $buyerService,
        #[MapRequestPayload] BuyerData $buyerData,
    ): JsonResponse {
        $apiUser = $this->getUser();

        if ($apiUser instanceof ApiUser) {
            $createdBuyer = $buyerService->createBuyer($buyerData, $apiUser);

            $data = $buyerService->getSerializedBuyer($createdBuyer, $apiUser);

            $response = new JsonResponse($data, Response::HTTP_CREATED, [], true);

            return $response;
        }

        throw new HttpException(404, 'User not found');
    }
}
