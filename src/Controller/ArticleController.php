<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @var ArticleRepository
     */
    private $articles;


    public function __construct(ArticleRepository $articles){
        $this->articles = $articles;

    }

    /**
     * @Route("/article", name="article")
     */
    public function index(): Response
    {
        $this->articles->find(1);
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
}
