<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use Faker;

class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i= 1;$i <= 20; $i++){
            $article = new article();
            $article->setAuthor($faker->name)
                ->setTitle($faker->text($maxNbChars = 50))
                ->setSummary($faker->text($maxNbChars = 100))
                ->setContent($faker->sentence(500,true))
                ->setPicture($faker->imageUrl(640,480))
                ->setCreatedAt($faker->dateTime('now'));

            $manager->persist($article);
        }
        $manager->flush();
    }
}
