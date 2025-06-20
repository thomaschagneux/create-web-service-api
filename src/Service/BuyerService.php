<?php

namespace App\Service;

use App\Dto\BuyerData;
use App\Entity\ApiUser;
use App\Entity\Buyer;
use App\Repository\BuyerRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BuyerService
{
    public function __construct(
        private readonly BuyerRepository $buyerRepository,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getSerializedBuyers(int $page, int $limit, ApiUser $apiUser): string
    {
        $userList = $this->buyerRepository->findPaginatedList($page, $limit, $apiUser);

        $context = SerializationContext::create()->setGroups(['getBuyerList']);

        return $this->serializer->serialize($userList, 'json', $context);
    }

    public function getSerializedBuyer(Buyer $buyer, ApiUser $apiUser): string
    {
        $context = SerializationContext::create()->setGroups(['getBuyer']);

        if ($apiUser->getId() !== $buyer->getApiUser()->getId()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Cet utilisateur n'est pas lié à ce client");
        }

        return $this->serializer->serialize($apiUser, 'json', $context);
    }

    public function createBuyer(BuyerData $buyerData, ApiUser $apiUser): Buyer
    {
        $newBuyer = new Buyer();

        $newBuyer
            ->setFirstName($buyerData->getFirstName())
            ->setLastName($buyerData->getLastName())
            ->setApiUser($apiUser);

        $this->entityManager->persist($newBuyer);
        $this->entityManager->flush();

        return $newBuyer;
    }
}
