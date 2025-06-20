<?php

namespace App\DataFixtures;

use App\Entity\ApiUser;
use App\Entity\Buyer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const ROLE_USER = 'ROLE_USER';
    private const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $apiUsers = $this->loadApiUsers($manager);
        $this->loadBuyers($manager, $apiUsers);
        $this->loadProducts($manager);

        $manager->flush();
    }

    /**
     * @return array<ApiUser>
     */
    private function loadApiUsers(ObjectManager $manager): array
    {
        $apiUsers = [];
        for ($i = 0; $i < 5; ++$i) {
            $apiUser = new ApiUser();
            $roles = [self::ROLE_USER, self::ROLE_ADMIN];
            $role = $i % 2;
            $apiUser->setRoles([$roles[$role]]);
            $apiUser->setEmail(0 === $role ? 'user'.($i + 1).'@mail.com' : 'admin'.($i + 1).'@mail.com');
            $apiUser->setPassword($this->userPasswordHasher->hashPassword($apiUser, 'pass123'));
            $apiUsers[] = $apiUser;
            $manager->persist($apiUser);
        }

        return $apiUsers;
    }

    /**
     * @param array<ApiUser> $apiUsers
     */
    private function loadBuyers(ObjectManager $manager, array $apiUsers): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; ++$i) {
            $apiUsersIndex = $i % count($apiUsers);
            $buyer = new Buyer();
            $buyer
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setApiUser($apiUsers[$apiUsersIndex]);

            $manager->persist($buyer);
        }
    }

    private function loadProducts(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; ++$i) {
            $product = new Product();
            $product->setName('Product '.$i + 1);
            $product->setReference('ref'.$i + 1);
            $manager->persist($product);
        }
    }
}
