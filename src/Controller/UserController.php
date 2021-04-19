<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifyInfoTypeFormType;
use App\Form\RegisterFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentsRepository;
use App\Repository\LikesRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->encoder = $encoder;
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Inscription Réussie, connectez vous maintenant !');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/Account", name="accountHome")
     * @IsGranted("ROLE_USER")
     */
    public function index(ArticleRepository $articleRepository, UserRepository $userRepository, CommentsRepository $commentsRepository): Response
    {
        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $comments = $commentsRepository->findBy(['user' => $LoggedUser]);
        $CommentedArticles = [];

        foreach ($comments as $comment) {
            if (!in_array($comment->getArticle(), $CommentedArticles)) {
                $CommentedArticles[] = $comment->getArticle();
            }
        }
        if (count($CommentedArticles) > 5) {
            array_splice($CommentedArticles, 5, count($CommentedArticles) - 5);
        }


        $likedArticles = $articleRepository->FindAllLikedArticles($LoggedUser);
        array_splice($likedArticles, 5, count($likedArticles) - 5);


        return $this->render('user/accountHome.html.twig', [
            'LikedArticles' => $likedArticles,
            'CommentedArticles' => $CommentedArticles
        ]);
    }

    /**
     * @Route("Account/personalInfo", name="accountInfo")
     * @IsGranted("ROLE_USER")
     */
    public function info(UserRepository $userRepository, Request $request): Response
    {
        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);

        $form = $this->createForm(ModifyInfoTypeFormType::class, $LoggedUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($LoggedUser);
            $doctrine->flush();
            $this->addFlash('success', 'Information modifiés !');
        }

        return $this->render('user/accountInfo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("Account/LikedArt/{page?1}", name="LikedArt")
     * @IsGranted("ROLE_USER")
     */
    public
    function LikedArt(LikesRepository $likesRepository, UserRepository $userRepository, $page): Response
    {
        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $likes = $likesRepository->findBy(['user' => $LoggedUser]);
        $likedArticles = [];

        foreach ($likes as $like) {
            $likedArticles[] = $like->getArticle();
        }
        $articleShowed = [];
        if (count($likedArticles) > $page * 10) {
            for ($i = ($page - 1) * 10; $i < $page * 10; $i++) {
                $articleShowed[] = $likedArticles[$i];
            }
        } else {
            for ($i = ($page - 1) * 10; $i < count($likedArticles); $i++) {
                $articleShowed[] = $likedArticles[$i];
            }
        }

        return $this->render('user/accountLiked.html.twig', [
            'Likedarticles' => $articleShowed,
            'pagination' => $page
        ]);
    }

    /**
     * @Route("Account/LikedComs/{page?1}", name="LikedComs")
     * @IsGranted("ROLE_USER")
     */
    public
    function CommentedArt(CommentsRepository $commentsRepository, UserRepository $userRepository, $page): Response
    {
        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $comments = $commentsRepository->findBy(['user' => $LoggedUser]);

        $CommentedArticles = [];

        foreach ($comments as $comment) {
            if (!in_array($comment->getArticle(), $CommentedArticles)) {
                $CommentedArticles[] = $comment->getArticle();
            }

        }
        $articleShowed = [];
        if (count($CommentedArticles) > $page * 10) {
            for ($i = ($page - 1) * 10; $i < $page * 10; $i++) {
                $articleShowed[] = $CommentedArticles[$i];
            }
        } else {
            for ($i = ($page - 1) * 10; $i < count($CommentedArticles); $i++) {
                $articleShowed[] = $CommentedArticles[$i];
            }
        }

        return $this->render('user/accountComs.html.twig', [
            'CommentedArticles' => $articleShowed,
            'commentaires'=>$comments,
            'pagination' => $page
        ]);
    }

    /**
     * @Route("/AccountAdmin", name="accountAdmin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexAdmin(ArticleRepository $articleRepository, UserRepository $userRepository): Response
    {
        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $articles = $articleRepository->findBy(['user' => $LoggedUser], ['Created_at' => 'desc']);

        if (count($articles) > 5) {
            array_splice($articles, 5, count($articles) - 5);
        }

        return $this->render('user/accountHomeAdmin.html.twig', [
            'MesArticles' => $articles
        ]);
    }

    /**
     * @Route("/AccountArticleAdmin/{page?1}", name="accountArticleAdmin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function articleAdmin(ArticleRepository $articleRepository, UserRepository $userRepository, $page): Response
    {
        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $articles = $articleRepository->findBy(['user' => $LoggedUser], ['Created_at' => 'desc']);

        if (count($articles) < 10) {
            $nbPage = 1;
        } else {
            $nbPage = count($articles) / 10;
        }
        $articleShowed = [];
        if (count($articles) > $page * 10) {
            for ($i = ($page - 1) * 10; $i < $page * 10; $i++) {
                $articleShowed[] = $articles[$i];
            }
        } else {
            for ($i = ($page - 1) * 10; $i < count($articles); $i++) {
                $articleShowed[] = $articles[$i];
            }
        }
        return $this->render('user/accountArticlesAdmin.html.twig', [
            'MesArticles' => $articleShowed,
            'pagination' => $nbPage,
        ]);

    }

    /**
     * @Route("/AccountAdmin/GestionCom/{page?1}", name="GestionComs")
     * @IsGranted("ROLE_ADMIN")
     * @param ArticleRepository $articleRepository
     * @param CommentsRepository $commentsRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function GestionCom(ArticleRepository $articleRepository, CommentsRepository $commentsRepository, UserRepository $userRepository, $page): Response
    {
        $LoggedUser = $userRepository->findOneBy(['email' => $this->getUser()->getUsername()]);
        $MesArticles = $articleRepository->findBy(['user' => $LoggedUser]);

        $Commententaires = [];
        foreach ($MesArticles as $article) {
            foreach ($article->getComments() as $comment) {
                $Commententaires[] = $comment;

            }
            if (count($Commententaires) < 10) {
                $nbPage = 1;
            } else {
                $nbPage = count($Commententaires) / 10;
            }
            $ComShowed = [];
            if (count($Commententaires) > $page * 10) {
                for ($i = ($page - 1) * 10; $i < $page * 10; $i++) {
                    $ComShowed[] = $Commententaires[$i];
                }
            } else {
                for ($i = ($page - 1) * 10; $i < count($Commententaires); $i++) {
                    $ComShowed[] = $Commententaires[$i];
                }
            }
        }
            return $this->render('user/GestionComsAdmin.html.twig', [
                'commentaires' => $ComShowed,
                'pagination' => $nbPage,
            ]);

    }

    /**
     * @Route("/AccountAdmin/validationCom", name="validationCom")
     * @IsGranted("ROLE_ADMIN")
     */
    public function validationCom(Request $request, CommentsRepository $commentsRepository):Response
    {

        $comValidated = $request->request->get('com_validated');
        $comRefused = $request->request->get('com_refused');

        $entityManager = $this->getDoctrine()->getManager();
        if(!is_null($comValidated)) {
            foreach ($comValidated as $value) {
                $com = $commentsRepository->findOneBy(['id' => $value]);
                $com->setState('validated');
                $entityManager->persist($com);
            }
        }
        if(!is_null($comRefused)) {
            foreach ($comRefused as $value) {
                $com = $commentsRepository->findOneBy(['id' => $value]);
                $com->setState('refused');
                $entityManager->persist($com);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('GestionComs');
    }
}
