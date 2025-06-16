<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ShowProductController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer un produit.
     */
    #[Route('/api/product/{id}', name: 'show_product', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return a product',
        content: new OA\JsonContent(
            ref: new Model(type: Product::class, groups: ['getProduct'])
        )
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The product ID',
        in: 'path',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag('Products')]
    #[IsGranted('ROLE_ADMIN')]
    public function showProduct(Product $product, ProductService $productService): JsonResponse
    {
        $jsonProduct = $productService->getProduct($product);

        return new JsonResponse($jsonProduct, 200, [], true);
    }
}
