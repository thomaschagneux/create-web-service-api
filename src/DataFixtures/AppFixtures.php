<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Entity\Customer;
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
        $customers = $this->loadCustomers($manager);
        $this->loadUsers($manager, $customers);
        $this->loadProducts($manager);

        $manager->flush();
    }

    /**
     * @return Customer[]
     */
    private function loadCustomers(ObjectManager $manager): array
    {
        $faker = Factory::create();
        $customers = [];
        for ($i = 0; $i < 5; ++$i) {
            $customer = new Customer();
            $customer->setName($faker->name);
            $customer->setApiKey($faker->uuid);
            $customers[] = $customer;
            $manager->persist($customer);
        }

        return $customers;
    }

    /**
     * @param Customer[] $customers
     */
    private function loadUsers(ObjectManager $manager, array $customers): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; ++$i) {
            /** @var Customer $customer */
            $customer = $faker->randomElement($customers);
            $user = new AppUser();
            $roles = [self::ROLE_USER, self::ROLE_ADMIN];
            $role = $i % 2;
            $user->setRoles([$roles[$role]]);
            $user->setEmail(0 === $role ? 'user'.($i + 1).'@mail.com' : 'admin'.($i + 1).'@mail.com');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'pass123'));
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setCustomer($customer);
            $manager->persist($user);
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
