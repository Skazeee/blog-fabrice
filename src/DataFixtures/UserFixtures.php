<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $faker->seed(0);


        $admin = new User();
        $admin->setEmail($faker->email)
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setPseudo('AdminPseudo')
            ->setPassword($this->encoder->encodePassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('Admin_ID',$admin);


        for ($i = 0; $i < 50; $i++){
        $user = new User();
        $user->setEmail($faker->email)
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setPseudo($faker->userName)
            ->setPassword($this->encoder->encodePassword($user, 'user'))
            ->setRoles(['ROLE_USER']);
        $manager->persist($user);
            $this->addReference('User_ID_'.$i,$user);

        }
        $manager->flush();
    }
}
