<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getSerializedProducts(int $page, int $limit): string
    {
        $productList = $this->productRepository->findPaginatedList($page, $limit);

        $context = SerializationContext::create()->setGroups(['getProductList']);

        return $this->serializer->serialize($productList, 'json', $context);
    }

    public function getSerializedProduct(Product $product): string
    {
        $context = SerializationContext::create()->setGroups(['getProduct']);

        return $this->serializer->serialize($product, 'json', $context);
    }
}
