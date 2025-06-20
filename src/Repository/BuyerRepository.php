<?php

namespace App\Repository;

use App\Entity\ApiUser;
use App\Entity\Buyer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Buyer>
 */
class BuyerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Buyer::class);
    }

    /**
     * @return Buyer[]
     */
    public function findPaginatedList(int $page, int $limit, ApiUser $apiUser): array
    {
        /** @var Buyer[] $result */
        $result = $this
            ->createQueryBuilder('b')
            ->andWhere('b.apiUser = :apiUser')
            ->setParameter('apiUser', $apiUser)
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
