<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comments;
use App\Entity\Likes;
use App\Form\CommentType;
use App\Form\CreateArticleType;
use App\Form\LikeTypeFormType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentsRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{


    public function __construct()
    {


    }

    /**
     * @Route("/article/{slug}", name="article_show")
     */
    public function index(int $slug, CommentsRepository $commentsRepository, ArticleRepository $articleRepository, Request $request, UserRepository $userRepository): Response
    {

        $articlesList = $articleRepository->FindLatest(4);
        $Article = $this->$articleRepository->find($slug);
        $comments = $commentsRepository->findBy(['article' => $Article]);

        if ($this->getUser()->getUsername()) {
            $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        }

        $comment = new Comments();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        $like = new Likes();
        $like->setUser($LoggedUser);
        $like->setArticle($Article);

        $formLike = $this->createForm(LikeTypeFormType::class, $like);
        if ($request->request->has('like_type_form')) {
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($like);
            $doctrine->flush();
            $this->addFlash('success', 'Vous avez aimé cet article !');
        }

        if ($formComment->isSubmitted() && $formComment->isValid() && $formComment->getName() == 'comment') {

            $comment
                ->setArticle($Article)
                ->setDate(date_create('now'))
                ->setUser($LoggedUser)
                ->setState('Waiting');
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($comment);
            $doctrine->flush();
            $this->addFlash('success', 'Commentaire bien envoyé !');
        }


        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $Article,
            'comments' => $comments,
            'ArticleRecent' => $articlesList,
            'formComment' => $formComment->createView(),
            'formLike' => $formLike->createView()
        ]);
    }

    /**
     * @Route("/article/{slug}/modify", name="article_modify")
     * @IsGranted("ROLE_ADMIN")
     * @param ArticleRepository $articleRepository
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function modifyArticle(ArticleRepository $articleRepository, Request $request, UserRepository $userRepository,CategoryRepository $categoryRepository, $slug): Response
    {

        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $article = $articleRepository->findOneBy(['id' => $slug]);
        $category = $article->getCategory()->getName();

        $form = $this->createForm(CreateArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $article
                ->setUser($LoggedUser)
                ->setCreatedAt(date_create('now'));

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($article);
            $doctrine->flush();
            $this->addFlash('success', 'article modifié !');
        }
        return $this->render('article/Modify.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),
            'category'=>$category,
            'article'=>$article

        ]);
    }

    /**
     * @Route("/admin/create", name="article_create")
     * @IsGranted("ROLE_ADMIN")
     * @param ArticleRepository $articleRepository
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function CreateArticle(Request $request, UserRepository $userRepository,CategoryRepository $categoryRepository): Response
    {

        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $article = new Article();
        $form = $this->createForm(CreateArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article
                ->setUser($LoggedUser)
                ->setCreatedAt(date_create('now'));
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($article);
            $doctrine->flush();
            $this->addFlash('success', 'article créé !');
        }
        return $this->render('article/Create.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/admin/delete/{slug}", name="article_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteArticle(ArticleRepository $articleRepository, $slug){

        $entityManager = $this->getDoctrine()->getManager();
        $article = $articleRepository->findOneBy(['id'=>$slug]);
        $entityManager->remove($article);
        $entityManager->flush();
        $this->addFlash('success', 'article supprimé !');
        return $this->redirectToRoute('accountAdmin');
    }
}
