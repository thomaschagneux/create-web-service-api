<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ShowProductController extends AbstractController
{
    #[Route('/api/product/{id}', name: 'show_product')]
    public function showProduct(Product $product, ProductService $productService): JsonResponse
    {
        $jsonProduct = $productService->getProduct($product);

        return new JsonResponse($jsonProduct, 200, [], true);
    }
}
