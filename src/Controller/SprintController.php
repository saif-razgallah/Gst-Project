<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Projet;
use App\Entity\Utilisateur;
use App\Entity\Sprint;
use App\Entity\Ticket;
use App\Form\SprintType;

class SprintController extends AbstractController
{
    /**
     * @Route("/sprint", name="sprint")
     */
    public function index()
    {
        return $this->render('sprint/index.html.twig', [
            'controller_name' => 'SprintController',
        ]);
    }

     /**
     * @Route("/sprint/list", name="sprint_list")
     */
    public function SprintTickets()
    {

        $em = $this->getDoctrine()->getManager();
        $usersession=$this->getUser();
        $result = $em->createQueryBuilder();
        $project_search = $result->select ('Util','proj')
            ->from('App\Entity\Utilisateur','Util')
            ->leftJoin ('Util.projet', 'proj')
            ->where('Util.id=:id')
            ->setParameter('id',$usersession->getId())
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $id_projet=$project_search[0]['projet']['id'];

        $repository1=$this->getDoctrine()->getManager()->getRepository(Ticket::class);
        $repository2=$this->getDoctrine()->getManager()->getRepository(Sprint::class);

        $Tickets=$repository1->findBy(['projet' => $id_projet,'sprint'=>array('date_field' => null)]);
        $Sprints=$repository2->findBy(['projet' => $id_projet]);

       //dump($TousLesTickets);die;
      
        return $this->render('sprint/ListeSprint.html.twig',[
            'Tickets'=>$Tickets,
            'Sprints'=>$Sprints, 
        ]);
       
    }

    /**
     * @Route("/sprint/new/{id}", name="sprint_new")
     */
    public function newSprint(Projet $projet,Request $request)
    {

    	$sprint= new sprint();
        $form=$this->createForm(SprintType::class,$sprint);

        //pour excuter la méthode POST
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
        if ($form ->isSubmitted() && $form ->isValid() )
            {   //dump($utilisateur);
                $user = $this->getUser();
                $sprint->setUtilisateur($user);
                $sprint->setProjet($projet);
                $sprint->setStatut('encours');
                $sprint->setDatecreation(new \DateTime('now'));
                $em->persist($sprint);
                $em->flush();
                $this->addFlash('success', 'Bien ajouté avec succés');
             return $this->redirectToRoute('sprint_list');
            }
              

        return $this->render('sprint/NewSprint.html.twig', [
            'controller_name' => 'SprintController',
            'form'=>$form->createView()

        ]);
    }



    /**
     * @Route("/sprint/edit/{id}", name="sprint.edit",methods="GET|POST")
     */
    public function editSprint(Sprint $sprint ,Request $request)
    {
        $form =$this->createForm(SprintType::class,$sprint);
        //pour exceuter la methode post
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();

        if ($form ->isSubmitted() && $form ->isValid())
            {	
                $em->flush();
                $this->addFlash('success','Bien modifié avec succés');
                return $this->redirectToRoute('sprint_list');

            }

        return $this->render('sprint/edit.html.twig',[
            'sprint'=>$sprint,
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/sprint/delete/{id}", name="sprint.delete",methods="DELETE")
     */

    // on a différenciée entre les 2 methodes pour ne pas confondre
    public function deleteSprint(Sprint $sprint)
    {
        $em=$this->getDoctrine()->getManager();
        $em->remove($sprint);
        $em->flush();
        $this->addFlash('success','Bien supprimé avec succés');
        
        return $this->redirectToRoute('sprint_list');
    }


    /**
     * @Route("/sprint/termine/{id}", name="sprint.termine",methods="GET|POST")
     */

    public function termineSprint(Request $request,$id)
    {
        
        $em=$this->getDoctrine()->getManager();
        $repository= $this->getDoctrine()->getRepository(Sprint::class);
        $sprint = $repository->find($id);
        
        dump($sprint);
        
        $sprint->setStatut('achevee');
        $em->persist($sprint);
        
        $em->flush();
        return $this->redirectToRoute('sprint_list');

    }


}
