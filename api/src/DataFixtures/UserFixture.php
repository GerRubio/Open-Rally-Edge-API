<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Hashed password
        $testingPasswordHash = '$argon2i$v=19$m=16,t=2,p=1$cGFzc3dvcmQ$A9HKT/FCm9ft8VCFgT4rVw';

        // User Peter
        $user_peter = new User('Peter', 'peter@api.com');

        $user_peter->setPassword($testingPasswordHash);
        $user_peter->setActive(true);
        $user_peter->setResetPasswordToken('123456');

        $manager->persist($user_peter);

        // User Brian
        $user_brian = new User('Brian', 'brian@api.com');

        $user_brian->setPassword($testingPasswordHash);
        $user_brian->setActive(true);

        $manager->persist($user_brian);

        // User Roger
        $user_roger = new User('Roger', 'roger@api.com');

        $user_roger->setPassword($testingPasswordHash);
        $user_roger->setActive(false);

        $manager->persist($user_roger);

        // Save
        $manager->flush();
    }
}