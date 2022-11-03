<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\ResetPasswordType;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Component\Form\Extension\Core\Type\DateType;





class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index()
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    /**
     * @Route("/profil/password", name="profil_util.password",methods="GET|POST")
     */
	public function editpassword(Request $request,UserPasswordEncoderInterface $encoder)

    {
    	$em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
    	$form = $this->createForm(ResetPasswordType::class,$user);
    	$form->handleRequest($request);
    	//dump('saif');

        if ($form->isSubmitted() && $form->isValid()) {

		     $old_pwd = $user->getoldPassword();
       		 $new_pwd = $user->getnewPassword();
		        //dump($old_pwd);   
		        //dump($new_pwd);

		    if ($encoder->isPasswordValid($user, $old_pwd)){
		    	 dump($new_pwd);
		    	 $newEncodedPassword = $encoder->encodePassword($user,$new_pwd);
		    	 
		    	 $user->setPassword($newEncodedPassword);
		    	 $em->persist($user);
                 $em->flush();
                 $this->addFlash('success', 'Votre mot de passe à bien été changé !');

		    }else {
            $form->addError(new FormError('Ancien mot de passe incorrect'));
        }

        }
    	return $this->render('profil/ResetPassword.html.twig', array(
    		'form' => $form->createView(),
    	));
    }

    /**
     * @Route("/profil/util", name="profil_util.edit",methods="GET|POST")
     */
    public function editUser(Request $request)
    {   
        $utilisateur= new utilisateur();
        $usersession=$this->getUser();
        
        $utilisateur= $this->getDoctrine()->getRepository(Utilisateur::class)
        ->find($usersession->getId());
            
        //dump($utilisateur);
        //pour exceuter la methode post
        $form=$this->createForm(UtilisateurType::class,$utilisateur);
        
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
   
        if ($form ->isSubmitted() && $form ->isValid())
            {   
                /** @var UploadedFile $uploadedFile */
            $uploadedFile=$form['imageFile']->getData();
            //dump($uploadedFile);
            if($uploadedFile)
            {
            $destination = $this->getParameter('upload_directory');
            $originalFilename=pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename =$originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($destination,$newFilename);
            
            $file = './uploads/'.$utilisateur->getPhoto();
                
                //dump($utilisateur->getPhoto());
                //dump(file_exists($file)); 

                if(file_exists($file)&&($utilisateur->getPhoto())) {
                    unlink($file);
                 }     
           
            $utilisateur->setPhoto($newFilename);

            }

                $em->flush();
                $this->addFlash('success','Bien modifié avec succés');

            }  

                $img =$utilisateur->getPhoto();

        return $this->render('profil/UpdatePersonal.html.twig',[
            'utilisateur'=>$utilisateur,
            'img'=>$img,
            'form'=>$form->createView()
        ]);
    }

}
