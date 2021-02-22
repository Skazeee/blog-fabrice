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
        $faker = Faker\Factory::create('fr_FR');
        for ($i=0;$i<10;$i++) {
            $category = new Category();
            $category->setName($faker->city);
            $manager->persist($category);
        }
        $this->addReference('Category_ID',$category);

        $manager->flush();
    }
}
