<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadProducts($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; ++$i) {
            $user = new AppUser();
            $roles = ['ROLE_USER', 'ROLE_ADMIN'];
            $role = 0 == $i % 2 ? 0 : 1;
            $user->setRoles([0 == $role ? 'ROLE_USER' : 'ROLE_ADMIN']);
            $user->setEmail(0 === $role ? 'user'.$i + 1 .'@mail.com' : 'admin'.$i + 1 .'@mail.com');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'pass123'));
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
