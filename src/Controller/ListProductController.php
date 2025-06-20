<?php

namespace App\Controller;

use App\Dto\PaginationQuery;
use App\Entity\Product;
use App\Service\ProductService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ListProductController extends AbstractController
{
    public function __construct(
        private readonly TagAwareCacheInterface $cache,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Cette méthode permet de récupérer l'ensemble des produits.
     *
     * @throws InvalidArgumentException
     */
    #[Route('api/product-list', name: 'list_product', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return list of products',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: Product::class, groups: ['getProductList', 'getProduct']),
            )
        )
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'The page we want to retrieve',
        in: 'query',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'The number of elements per page',
        in: 'query',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Tag('Products')]
    #[IsGranted('ROLE_USER')]
    public function listProduct(Request $request, ProductService $productService): JsonResponse
    {
        $pageQuery = $request->query->get('page', '1');
        $limitQuery = $request->query->get('limit', '100');

        $paginationQuery = new PaginationQuery(
            (string) $pageQuery,
            (string) $limitQuery,
        );

        $errors = $this->validator->validate($paginationQuery);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $idCache = 'product_list_page_'.$paginationQuery->getPage().'_limit_'.$paginationQuery->getLimit();
        $isCached = 'true';
        $jsonProductList = $this->cache->get($idCache, function (ItemInterface $item) use ($productService, $paginationQuery, &$isCached) {
            $isCached = 'false';
            $item->expiresAfter(60 * 5);

            return $productService->getSerializedProducts($paginationQuery->getPageAsInt(), $paginationQuery->getLimitAsInt());
        });

        return new JsonResponse($jsonProductList, 200, ['x-is-cached' => $isCached], true);
    }
}
