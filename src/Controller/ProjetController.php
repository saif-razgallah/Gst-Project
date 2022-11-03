<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Projet;
use App\Entity\Sprint;
use App\Entity\Ticket;
use App\Entity\Utilisateur;

use App\Form\ProjetType;
use App\Repository\ProjetRepository;


class ProjetController extends AbstractController
{

    /**
     * @Route("/projet/list", name="projet_list")
     */
    public function listprojets()
    {
        $em = $this->getDoctrine()->getManager();
        $usersession=$this->getUser();        
        $result = $em->createQueryBuilder();

        
        $Projets = $result->select('P','S','T')
            ->from('App\Entity\Projet','P')
            ->leftJoin ('P.sprints', 'S')
            ->leftJoin ('S.tickets', 'T')
            ->where('P.utilisateur=:id or P.responsable=:id or T.responsable=:id' )
            ->setParameter('id',$usersession->getId())
            ->getQuery()
            ->getResult();
       // dump($Projets);

         

        return $this->render('projet/listprojet.html.twig',['Projets'=>$Projets, 
        ]);
       
    }


    /**
     * @Route("/projet", name="projet_new")
     */
    public function newProjet(Request $request)
    {

    	$projet= new projet();
        $form=$this->createForm(ProjetType::class,$projet);

        //pour excuter la méthode POST
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
         $user = $this->getUser();

                
        if ($form ->isSubmitted() && $form ->isValid() )
            {   

                $projet->setUtilisateur($user);
                $projet->setStatut('nonentame');
                $em->persist($projet);
                $em->flush();
                $this->addFlash('success', 'Bien crée avec succés');
             return $this->redirectToRoute('projet_new');
            }

    
         

        return $this->render('projet/NewProjet.html.twig', [
            'controller_name' => 'ProjetController',
            'form'=>$form->createView()

        ]);
    }


    /**
     * @Route("/projet/list/{id}", name="projet_list.edit",methods="GET|POST")
     */
    public function edit(Projet $projet ,Request $request)
    {
        $form =$this->createForm(ProjetType::class,$projet);
        //pour exceuter la methode post
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();

        if ($form ->isSubmitted() && $form ->isValid())
            {
                $em->flush();
                $this->addFlash('success','Bien modifié avec succés');
                return $this->redirectToRoute('projet_list.edit');

            }

        return $this->render('projet/edit.html.twig',[
            'projet'=>$projet,
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/projet/list/{id}", name="projet_list.delete",methods="DELETE")
     */

    // on a différenciée entre les 2 methodes pour ne pas confondre
    public function delete(Projet $projet)
    {
        $em=$this->getDoctrine()->getManager();
        $em->remove($projet);
        $em->flush();
        $this->addFlash('success','Bien supprimé avec succés');
        
        return $this->redirectToRoute('projet_list');
    }


}
