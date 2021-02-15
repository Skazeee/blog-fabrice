<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class Users extends Fixture
{
    private $encoder;

    public function  __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $faker->seed(0);

        $admin = new User();
        $admin->setEmail('test@test.com')
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setPseudo('AdminPseudo')
            ->setPassword($this->encoder->encodePassword($admin,'admin'))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);



        $user = new User();
        $user->setEmail('test@ex.com')
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setPseudo('UserPseudo')
            ->setPassword($this->encoder->encodePassword($user,'admin'))
            ->setRoles(['ROLE_USER']);

        $manager->persist($user);

        $manager->flush();
    }
}
