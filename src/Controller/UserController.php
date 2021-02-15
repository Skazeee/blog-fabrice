<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user= new User();
        $form =$this->createForm(RegisterFormType::class,$user);

        $form->handleRequest($request);
       if($form->isSubmitted() && $form->isValid()){
           $this->encoder = $encoder;
           $user = $form->getData();
           $user->setPassword($this->encoder->encodePassword($user,$user->getPassword()));

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
}
