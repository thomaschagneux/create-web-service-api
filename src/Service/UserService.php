<?php

namespace App\Service;

use App\Dto\UserData;
use App\Entity\AppUser;
use App\Entity\Customer;
use App\Repository\AppUserRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly AppUserRepository $appUserRepository,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly CustomerRepository $customerRepository,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getUsers(int $page, int $limit, Customer $customer): string
    {
        $userList = $this->appUserRepository->findPaginatedList($page, $limit, $customer);

        $context = SerializationContext::create()->setGroups(['getUserList']);

        return $this->serializer->serialize($userList, 'json', $context);
    }

    public function getUser(Customer $customer, AppUser $user): string
    {
        $context = SerializationContext::create()->setGroups(['getUser']);

        if ($user->getCustomer()->getId() !== $customer->getId()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Cet utilisateur n'est pas lié à ce client");
        }

        return $this->serializer->serialize($user, 'json', $context);
    }

    public function createUser(UserData $userData): AppUser
    {
        $user = new AppUser();
        $password = $this->userPasswordHasher->hashPassword($user, $userData->getPassword());

        /** @var Customer $customer */
        $customer = $this->customerRepository->find($userData->getCustomerId());

        $user
            ->setEmail($userData->getEmail())
            ->setFirstName($userData->getFirstName())
            ->setLastName($userData->getLastName())
            ->setCustomer($customer)
            ->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
