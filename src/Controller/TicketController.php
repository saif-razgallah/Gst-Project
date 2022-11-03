<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Projet;
use App\Entity\Utilisateur;
use App\Entity\Ticket;
use App\Form\TicketType;
use App\Entity\Commentaire;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;



class TicketController extends AbstractController
{
    /**
     * @Route("/ticket", name="ticket")
     */
    public function index()
    {
        return $this->render('ticket/index.html.twig', [
            'controller_name' => 'TicketController',
        ]);
    }

     /**
     * @Route("/ticket/list", name="ticket_list")
     */
    public function listTickets()
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

        $repository=$this->getDoctrine()->getManager()->getRepository(Ticket::class);

        $MesTicketsCrees=$repository->findBy(['utilisateur' => $this->getUser()->getId(),'projet' =>$id_projet]);
        $MesTicketsOuverts=$repository->findBy(['utilisateur' => $this->getUser()->getId(),'statut'=>array('encours','nonentame'),'projet' => $id_projet]);
        $MesTicketsTermines=$repository->findBy(['utilisateur' => $this->getUser()->getId(),'statut'=>'achevee','projet' => $id_projet]);
        $TousLesTickets=$repository->findBy(['projet' => $id_projet]);

        $Ticketsfaits=$repository->findBy(['responsable' => $this->getUser()->getId(),'projet' => $id_projet]);

        return $this->render('ticket/ListesTickets.html.twig',[
        	'MesTicketsCrees'=>$MesTicketsCrees, 
            'MesTicketsOuverts'=>$MesTicketsOuverts, 
            'MesTicketsTermines'=>$MesTicketsTermines, 
            'TousLesTickets'=>$TousLesTickets, 
            'Ticketsfaits'=>$Ticketsfaits, 
        ]);
       
    }

    /**
     * @Route("/ticket/new/{id}", name="ticket_new",methods="GET|POST")
     */
    public function newticket(Projet $projet,Request $request)
    {

        $ticket= new ticket();
        $form=$this->createForm(TicketType::class,$ticket);

        //pour excuter .la méthode POST
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
        if ($form ->isSubmitted() && $form ->isValid() )
            {   //dump($utilisateur);
                $user = $this->getUser();
                $ticket->setUtilisateur($user);
                $ticket->setStatut('nonentame');
                $ticket->setProjet($projet->getId());
                $ticket->setDatecreation(new \DateTime('now'));
                $em->persist($ticket);
                $em->flush();
             return $this->redirectToRoute('ticket_list');
            }
              

        return $this->render('ticket/Newticket.html.twig', [
            'controller_name' => 'TicketController',
            'form'=>$form->createView()

        ]);
    }

    /**
     * @Route("/ticket/edit/{id}", name="ticket_list.edit",methods="GET|POST")
     */
    public function edit(Ticket $ticket ,Request $request)
    {
        $form =$this->createForm(TicketType::class,$ticket);
        //pour exceuter la methode post
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();

        if ($form ->isSubmitted() && $form ->isValid())
            {
                $em->flush();
                $this->addFlash('success','Bien modifié avec succés');
                return $this->redirectToRoute('ticket_list');

            }

        return $this->render('ticket/edit.html.twig',[
            'projet'=>$ticket,
            'form'=>$form->createView()
        ]);
    }

     /**
     * @Route("/ticket/delete/{id}", name="ticket_list.delete",methods="DELETE")
     */

    // on a différenciée entre les 2 methodes pour ne pas confondre
    public function delete(Ticket $ticket)
    {
        $em=$this->getDoctrine()->getManager();
        $em->remove($ticket);
        $em->flush();
        $this->addFlash('success','Bien supprimé avec succés');
        
        return $this->redirectToRoute('ticket_list');
    }

    /**
     * @Route("/ticket/list/show/{id}", name="ticket.show",methods="GET|POST")
     */
    public function ShowTicket(Request $request,$id )
    {

        $repository=$this->getDoctrine()->getManager()->getRepository(Ticket::class);
        $ticket=$repository->find($id); 

        dump($ticket);
        
        /*------------------------------- id-destinataire  ---------------------------------*/


        /*---------------------------id_expediteur-----------------------------*/
        
        $usersession=$this->getUser();
        $expediteur= $this->getDoctrine()->getRepository(Utilisateur::class)
        ->find($usersession->getId());
         dump($expediteur);
        /*-------------------------- New Commentaire -----------------------------------------*/
        $Commentaire = new Commentaire();
        $form =$this->createFormBuilder($Commentaire)
                    ->add('contenu', TextareaType::class, [
                           'attr' => array('cols' => '75', 'rows' => '5','style'=>"resize:none"),
                                ])
                    ->getForm();  

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
            
        if ($form->isSubmitted() && $form->isValid())
         {
            $expediteur->addCommentaire($Commentaire);
            $ticket->addCommentaire($Commentaire);
            $Commentaire->setDateEnvoi(new \DateTime('Africa/Tunis'));

            $em->persist($Commentaire);
            $em->flush();
            return $this->redirectToRoute('ticket.show',array('id' => $id));
  
        }  

         /*------------------------- Affiche Commentaire ----------------------------------------*/
        $Commentaires= new Commentaire();
        $results = $em->createQueryBuilder();

        $Commentaires = $results->select('c','u','t')
            ->from('App\Entity\Commentaire','c')
            ->leftJoin ('c.utilisateur', 'u')
            ->leftJoin ('c.ticket', 't')
            ->where('c.ticket=:id')
            ->addOrderBy('c.date_envoi', 'ASC')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);       
        dump($Commentaires);
        /*---------------------------------------------------------------------------------------*/ 


        return $this->render('ticket/ShowTicket.html.twig',[
            'form' => $form->createView(),
            'Commentaires'=>$Commentaires,
            'ticket'=>$ticket,
        ]);
    }


     /**
     * @Route("/ticket/encours/{id}", name="ticket.encours",methods="GET|POST")
     */

    public function encoursTicket(Request $request,$id)
    {
        
        $em=$this->getDoctrine()->getManager();
        $repository= $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);
        
        dump($ticket);
        
        $ticket->setStatut('encours');
        $em->persist($ticket);
        
        $em->flush();
        return $this->redirect($this->generateUrl('ticket.show', array('id' => $id)));

       // return $this->redirectToRoute('ticket.show',$id);
    }

    /**
     * @Route("/ticket/termine/{id}", name="ticket.termine",methods="GET|POST")
     */

    public function termineTicket(Request $request,$id)
    {
        
        $em=$this->getDoctrine()->getManager();
        $repository= $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);
        
        dump($ticket);
        
        $ticket->setStatut('achevee');
        $em->persist($ticket);
        
        $em->flush();
        return $this->redirect($this->generateUrl('ticket.show', array('id' => $id)));

       // return $this->redirectToRoute('ticket.show',$id);
    }


    /**
     * @Route("/sprint/retirer/{id}", name="ticket.retirer",methods="GET|POST")
     */

    public function retirerTicket(Request $request,$id)
    {
        
        $em=$this->getDoctrine()->getManager();
        $repository= $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);
        
        dump($ticket);
        
        $ticket->setSprint(null);
        $em->persist($ticket);
        
        $em->flush();
        return $this->redirectToRoute('sprint_list');

       // return $this->redirectToRoute('ticket.show',$id);
    }


    /**
     * @Route("/sprint/addbacklog/{id}", name="addbacklog",methods="GET|POST")
     */

    public function addbacklog(Ticket $ticket ,Request $request)
    {
        
        $form =$this->createFormBuilder($ticket)
                    ->add('sprint')
                    ->getForm();    
        //pour exceuter la methode post
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();

        if ($form ->isSubmitted() && $form ->isValid())
            {
                $em->flush();
                $this->addFlash('success','Bien modifié avec succés');
                return $this->redirectToRoute('sprint_list');

            }

        return $this->render('ticket/editbacklog.html.twig',[
            'projet'=>$ticket,
            'form'=>$form->createView()
        ]);
    }


}
