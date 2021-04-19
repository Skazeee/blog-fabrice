<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\Likes;
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



        for ($g = 0; $g < 10; $g++) {
            $article = new Article();
            $article
                ->setUser($this->getReference('Admin_ID'))
                ->setTitle($faker->text($maxNbChars = 50))
                ->setSummary($faker->text($maxNbChars = 100))
                ->setContent($faker->sentence(250, true))
                ->setPicture('https://picsum.photos/800/200')
                ->setCreatedAt($faker->dateTime('now'))
                ->setCategory($this->getReference('Category_ID_' . random_int(0, 6)));
            $manager->persist($article);

            for ($h = 0; $h <= 5; $h++) {
                $comment = new Comments();
                $comment
                    ->setUser($this->getReference('User_ID_'.random_int(0,49)))
                    ->setArticle($article)
                    ->setContent($faker->sentence(100, true))
                    ->setDate($faker->dateTime('now'))
                    ->setState("waiting");
                $manager->persist($comment);
            }
        }

        for ($i = 0; $i < 90; $i++) {
            $article = new Article();
            $article
                ->setUser($this->getReference('User_ID_'.random_int(0,9)))
                ->setTitle($faker->text($maxNbChars = 50))
                ->setSummary($faker->text($maxNbChars = 100))
                ->setContent($faker->sentence(250, true))
                ->setPicture('https://picsum.photos/800/200')
                ->setCreatedAt($faker->dateTime('now'))
                ->setCategory($this->getReference('Category_ID_'.random_int(0,6)));

            $manager->persist($article);

            for ($j = 0; $j <= 5; $j++) {
                $comment = new Comments();
                $comment
                    ->setUser($this->getReference('User_ID_'.random_int(0,49)))
                    ->setArticle($article)
                    ->setContent($faker->sentence(100, true))
                    ->setDate($faker->dateTime('now'))
                    ->setState("validated");
                $manager->persist($comment);
            }
            for ($k = 0; $k < 35; $k++) {
                $like = new likes();
                $like
                    ->setArticle($article)
                    ->setUser($this->getReference('User_ID_'.random_int(0,49)));
                $manager->persist($like);
            }
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
