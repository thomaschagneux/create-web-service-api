<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SerializerInterface $serializer,
        private readonly TagAwareCacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getProducts(int $page, int $limit): string
    {
        $productList = $this->productRepository->findPaginatedList($page, $limit);

        $context = SerializationContext::create()->setGroups(['getProductList']);

        return $this->serializer->serialize($productList, 'json', $context);
    }

    public function getProduct(Product $product): string
    {
        $idCache = 'product_'.$product->getId();

        $cachedProduct = $this->cache->get($idCache, function (ItemInterface $item) use ($product) {
            echo "Le produit n'est pas dans le cache, il est mis en cache \n";
            $customTime = 60 * 5;
            $item->expiresAfter($customTime);
            $item->tag(['product']);

            return $product;
        });

        $context = SerializationContext::create()->setGroups(['getProduct']);

        return $this->serializer->serialize($cachedProduct, 'json', $context);
    }
}
