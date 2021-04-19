<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $randomCat = ["Food", "Genshin", "Cs:Go","Nature","Dofus","Animaux","Cinema"];
        $faker = Faker\Factory::create('fr_FR');
        for ($i=0;$i< count($randomCat) ;$i++) {
            $category = new Category();
            $category->setName($randomCat[$i]);
            $manager->persist($category);

            $this->addReference('Category_ID_'.$i,$category);
        }


        $manager->flush();
    }
}
