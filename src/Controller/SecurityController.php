<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Utilisateur;
use App\Form\RegistrationType;



class SecurityController extends AbstractController
{
  
    /**
     * @Route("/inscription",name="security_registration")
     */
    public function registration(Request $request,UserPasswordEncoderInterface $encoder)
    {

        $utilisateur= new utilisateur();
        $form=$this->createForm(RegistrationType::class,$utilisateur);

        //pour excuter la méthode POST
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
        if ($form ->isSubmitted() && $form ->isValid() )
            {   //dump($utilisateur);
                $hash=$encoder->encodePassword($utilisateur,$utilisateur->getPassword());
                $utilisateur->setPassword($hash);
                $em->persist($utilisateur);
                $em->flush();
                return $this->redirectToRoute('acceuil');
            }   

        return $this->render('security/registration.html.twig',[
            'utilisateur'=>$utilisateur,
            'form'=>$form->createView()
        ]);

    }

    /** 
     * @Route("/connexion",name="security_login")
     */ 
    
    public function login(AuthenticationUtils $authenticationUtils) 
    {

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                return $this->redirectToRoute('template');
        }

         // get the login error if there is one 
        $error = $authenticationUtils->getLastAuthenticationError();
         // last username entered by the user 
        $lastUsername = $authenticationUtils->getLastUsername(); 

        return $this->render('Acceuil/acceuil.html.twig', 
            ['lastUsername'=>$lastUsername,'error' => $error]
        ); 
    }


    /**
     * @Route("/deconnexion",name="security_logout")
     */

    public function logout()
    {

    }

}
