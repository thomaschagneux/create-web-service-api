<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function Symfony\Component\String\u;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $user = new AppUser();
            $roles = ['ROLE_USER', 'ROLE_ADMIN'];
            $role = array_rand($roles);
            $user->setRoles([$role == 0 ? 'ROLE_USER' : 'ROLE_ADMIN']);
            $user->setEmail($role === 0 ? 'user'.$i + 1 .'@mail.com' : 'admin'. $i + 1 .'@mail.com');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'pass123'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
