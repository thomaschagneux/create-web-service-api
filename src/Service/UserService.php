<?php

namespace App\Service;

use App\Entity\AppUser;
use App\Entity\Customer;
use App\Repository\AppUserRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
    public function getUsers(int $page, int $limit, Customer $customer): string
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

    public function getUser(Customer $customer, AppUser $user): string
    {
        $context = SerializationContext::create()->setGroups(['getUser']);

        if ($user->getCustomer()->getId() !== $customer->getId()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Cet utilisateur n'est pas lié à ce client");
        }

        $idCache = 'user_'.$user->getId().'_customer_'.$customer->getId();
        $userCached = $this->cache->get($idCache, function (ItemInterface $item) use ($user) {
            echo "L'élément n'est pas dans le cache, il est mis en cache \n";
            $customTime = 60 * 5;
            $item->expiresAfter($customTime);
            $item->tag(['user_show']);

            return $user;
        });

        return $this->serializer->serialize($userCached, 'json', $context);
    }
}
