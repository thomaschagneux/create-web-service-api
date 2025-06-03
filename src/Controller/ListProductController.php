<?php

namespace App\Controller;

use App\Service\ProductService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ListProductController extends AbstractController
{
    /**
     * @throws InvalidArgumentException
     */
    #[Route('api/product-list', name: 'list_product')]
    public function listProduct(Request $request, ProductService $productService): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        if (!ctype_digit($page) || !ctype_digit($limit)) {
            return new JsonResponse(['message' => 'Page or limit is not numeric'], 400);
        }

        $jsonProductList = $productService->getProducts((int) $page, (int) $limit);

        return new JsonResponse($jsonProductList, 200, [], true);
    }
}
