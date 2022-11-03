<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Projet;
use App\Entity\Utilisateur;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class AcceuilController extends AbstractController
{
    /**
     * @Route("/", name="acceuil")
     */
    public function index()
    {	
    	//$error='' ;

        return $this->render('Acceuil\acceuil.html.twig', [
            'controller_name' => 'AcceuilController',
            //'error' => $error,
        ]);
    }

     /**
     * @Route("/template", name="template")
     */
    public function ShowTemplate()
    {

        return $this->render('template.html.twig', [
            'controller_name' => 'AcceuilController',
        ]);
    }

     /**
     * @Route("/projet/liste/{id}", name="template.projet",methods="GET|POST")
     */
    public function viewProjet(Projet $myproject )
    {
        
        $repository=$this->getDoctrine()->getManager()->getRepository(Projet::class);
        $projets=$repository->find($myproject);

        $em = $this->getDoctrine()->getManager();

        $utilisateur= new utilisateur();
        $usersession=$this->getUser();
        $utilisateur= $this->getDoctrine()->getRepository(Utilisateur::class)
        ->find($usersession->getId());


        $projets->addProjetact($utilisateur,$myproject);
        $em->persist($utilisateur);
        $em->flush();

        return $this->redirectToRoute('projet_list');

    }

    /**
     * @Route("/calculatrice", name="calculatrice")
     */
    public function calculatrice()
    {   

        return $this->render('Acceuil\calculatrice.html.twig', [
            'controller_name' => 'AcceuilController',
        ]);
    }


    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {   

        $em = $this->getDoctrine()->getManager();
        $utilisateur= new utilisateur();
        $usersession=$this->getUser();
        $utilisateur= $this->getDoctrine()->getRepository(Utilisateur::class)
        ->find($usersession->getId());

        $result = $em->createQueryBuilder();
        $project_search = $result->select ('Util','proj')
            ->from('App\Entity\Utilisateur','Util')
            ->leftJoin ('Util.projet', 'proj')
            ->where('Util.id=:id')
            ->setParameter('id',$usersession->getId())
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $id_projet=$project_search[0]['projet']['id'];
            

        /*-----------Nbr_Tickets-------------------------*/
        $Tickets = $result->select('T','UT','RE')
            ->from('App\Entity\Ticket','T')
            ->leftJoin ('T.utilisateur', 'UT')
            ->leftJoin ('T.responsable', 'RE')
            ->where('T.projet=:id')
            ->setParameter('id',$id_projet)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
             $total_Nbr_Tickets =count($Tickets);  
            
        dump($Tickets);          
        //dump($total_Nbr_Tickets);

        /*-----------Nbr_Sprints-------------------------*/
        $Sprints = $result->select('S')
            ->from('App\Entity\Sprint','S')
            ->where('S.projet=:id')
            ->setParameter('id',$id_projet)
            ->getQuery()
            ->getResult();
        $total_Nbr_Sprints =count($Sprints);    
        //dump($total_Nbr_Sprints);

        /*-----------Nbr_Membres-------------------------*/

       $Membre = $result->select ('P','U','R')
            ->from('App\Entity\Projet','P')
            ->leftJoin ('P.utilisateur', 'U')
            ->leftJoin ('P.responsable', 'R')
            ->where('P.id=:id')
            ->setParameter('id',$id_projet)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $membres = array();

        if ($Membre != null)  
         {

        $id_utilisateur1=$Membre[0]['utilisateur']['id'];
        $id_utilisateur2=$Membre[0]['responsable']['id'];
        //dump($id_utilisateur1); 8
        //dump($id_utilisateur2); 7

        $membres = array();
        $membres[] = $id_utilisateur1;
        $membres[] = $id_utilisateur2;
        $taille_membres =count($membres);
        //dump($membres[0]);
        //dump($Tickets);

        
        for($i=0;  $i<$total_Nbr_Tickets; $i++)
        {   
            $trouve1 = false; 
            $trouve2 = false; 

            for($j=0;$j<count($membres);$j++)
            {

                if ($Tickets[$i]['utilisateur']['id'] == $membres[$j])
                { 
                    $trouve1 = true; 
                 }



                if ($Tickets[$i]['responsable']['id'] == $membres[$j] )
                {
                    $trouve2 = true; 
                 }

            }
            if ($trouve1 == false and $Tickets[$i]['utilisateur'] != null)
                {   
                    $membres[] = $Tickets[$i]['utilisateur']['id'];

                }
            if ($trouve2 == false and $Tickets[$i]['responsable'] != null)
                {
                    $membres[] = $Tickets[$i]['responsable']['id'];
                    
                }    
                    
        }
        }
        $nbr_membre =count($membres);
        dump($membres);


        /*-----------------------Nbr_Commenatires-------------------------*/

        $Nbr_Commenatires = $result->select('C')
            ->from('App\Entity\Commentaire','C')
            ->leftJoin ('C.ticket', 't')
            ->where('t.projet=:id')
            ->setParameter('id',$id_projet)
            ->getQuery()
            ->getResult(); 
        $total_Nbr_Commenatires =count($Nbr_Commenatires);  


        /*-----------------------Projet-------------------------*/
        
        $mon_projett = $result->select('Pr')
            ->from('App\Entity\Projet','Pr')
            ->where('Pr.id=:id')
            ->setParameter('id',$id_projet)
            ->getQuery()
            ->getResult();
        dump($mon_projett);

        /*---------------Tous les utilisateurs -----------------*/

        $TousLesUtilisateurs =  $result->select('users')
                    ->from('App\Entity\Utilisateur','users')
                    ->getQuery()
                    ->getResult();
            

        return $this->render('Acceuil\dashboard.html.twig', [
            'membres' => $membres,
            'TousLesUtilisateurs' =>$TousLesUtilisateurs,
            'nbr_membre'=>$nbr_membre,
            'mon_projett'=>$mon_projett,
            'total_Nbr_Commenatires'=>$total_Nbr_Commenatires,
            'total_Nbr_Tickets'=>$total_Nbr_Tickets,
            'total_Nbr_Sprints'=>$total_Nbr_Sprints,
            'controller_name' => 'AcceuilController',
        ]);
    }


}
