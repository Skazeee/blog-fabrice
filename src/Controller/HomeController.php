<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ArticleRepository $articles
     * @return Response
     */
    public function index(ArticleRepository $articles): Response
    {

        $articlesList = $articles->FindLatest(10);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController', 'articles' => $articlesList
        ]);
    }
}
