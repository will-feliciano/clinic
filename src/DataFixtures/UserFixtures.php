<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setUsername('user')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$2xd8Hm5jVZp6j8/g0wEZ4g$IA7e133LOcUXce3vOp1WVEr/KUZo/iefM6Z0tyX1k+8');

        $manager->persist($user);
        $manager->flush();
    }
}
