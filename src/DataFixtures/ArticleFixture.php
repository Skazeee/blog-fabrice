<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use Faker;

class ArticleFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $randomCat = ["Food", "Genshin", "Cs:Go"];

        for ($i = 1; $i <= 20; $i++) {
            $article = new Article();
            $article
                ->setUser($this->getReference('User_ID'))
                ->setTitle($faker->text($maxNbChars = 50))
                ->setSummary($faker->text($maxNbChars = 100))
                ->setContent($faker->sentence(500, true))
                ->setPicture($faker->imageUrl(640, 480))
                ->setCreatedAt($faker->dateTime('now'))
                ->setCategory($this->getReference('Category_ID'));

            $manager->persist($article);
        }
        $manager->flush();

    }
    public function getDependencies(): array
    {
        return[
            UserFixtures::class, CategoryFixtures::class,
        ];
    }
}
