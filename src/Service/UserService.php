<?php

namespace App\Service;

use App\Entity\Customer;
use App\Repository\AppUserRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserService
{
    public function __construct(
        private readonly AppUserRepository $appUserRepository,
        private readonly TagAwareCacheInterface $cache,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getUser(int $page, int $limit, Customer $customer): string
    {
        $idCache = 'user_list_page_'.$page.'_limit_'.$limit;

        $userList = $this->cache->get($idCache, function (ItemInterface $item) use ($page, $limit, $customer) {
            echo "L'élement n\'est pas dans le cache, il est mis en cache \n";
            $customTime = 60 * 5;
            $item->expiresAfter($customTime);
            $item->tag(['user_list']);

            return $this->appUserRepository->findPaginatedList($page, $limit, $customer);
        });

        $context = SerializationContext::create()->setGroups(['getUserList']);

        return $this->serializer->serialize($userList, 'json', $context);
    }
}
