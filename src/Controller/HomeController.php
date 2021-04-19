<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/{page?1}", name="home",requirements={"page"="\d+"} )
     * @return Response
     */
    public function index(ArticleRepository $articles, $page): Response
    {
        $articlesList = $articles->FindAllOrdered('Created_at',"desc");
        $nbPage = count($articlesList) / 10;
        $articleShowed = [];
        if (count($articlesList) > $page * 10) {
            for ($i = ($page - 1) * 10; $i < $page * 10; $i++) {
                $articleShowed[] = $articlesList[$i];
            }
        } else {
            for ($i = ($page - 1) * 10; $i < count($articlesList); $i++) {
                $articleShowed[] = $articlesList[$i];
            }
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'articles' => $articleShowed,
            'pagination' => $nbPage,
        ]);
    }
}
