<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(
 * fields={"email"},
 * message="L'email que vous avez indiqué est dejà utilisé !"
 *)
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_naissance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8",minMessage="Votre mot de passe doit faire minimum 8 caractéres")     
     */
    private $password;
    
    /**
     * @Assert\EqualTo(propertyPath="password",message="vous n'avez pas tapé le meme mot de passe")
     */
    public $confirm_password;

     /**
     * @Assert\Length(
     *     min = 8,
     *     minMessage = "Password should by at least 8 chars long"
     * )
     */
     protected $oldPassword;

    /**
     * @Assert\Length(
     *     min = 8,
     *     minMessage = "Password should by at least 8 chars long"
     * )
     */
     protected $newPassword;




    protected $captchaCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fonction;

    /**
     * @ORM\OneToMany(targetEntity=Projet::class, mappedBy="utilisateur")
     */
    private $projets;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="utilisateur")
     */
    private $tickets;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="responsable")
     */
    private $responsable;

    /**
     * @ORM\OneToMany(targetEntity=Projet::class, mappedBy="responsable")
     */
    private $chefequipe;

    /**
     * @ORM\OneToMany(targetEntity=Sprint::class, mappedBy="utilisateur")
     */
    private $sprints;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="utilisateur")
     */
    private $commentaires;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="expediteur")
     */
    private $expediteurs;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="destinataire")
     */
    private $destinataires;

    /**
     * @ORM\ManyToOne(targetEntity=Projet::class, inversedBy="projetact")
     */
    private $projet;



    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->responsable = new ArrayCollection();
        $this->chefequipe = new ArrayCollection();
        $this->sprints = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->expediteurs = new ArrayCollection();
        $this->destinataires = new ArrayCollection();
    }


    public function getCaptchaCode()
    {
      return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
      $this->captchaCode = $captchaCode;
    }

    public function getoldPassword(): ?string
    {
        return $this->oldPassword;
    }

     public function getnewPassword(): ?string
    {
        return $this->newPassword;
    }

     public function setnewPassword($newPassword)
    {

    $this->newPassword=$newPassword;
    }

    public function setoldPassword($oldPassword)
    {

    $this->oldPassword=$oldPassword;
    }

    // ... //

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(?\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles()
    {
        return ['Role_USER'];
    }



    /**/

    public function getPlainPassword() 
    {
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

     public function eraseCredentials(){
        
    }

    public function getSalt(){
    }

    public function getUsername(): ?string
    {
        return $this->nom;
    }

    public function setUsername(string $nom): self
    {
        $this->username = $nom;

        return $this;
    }

     public function __toString()
    {

        return $this->nom;
    }

     public function getFonction(): ?string
     {
         return $this->fonction;
     }

     public function setFonction(?string $fonction): self
     {
         $this->fonction = $fonction;

         return $this;
     }

     /**
      * @return Collection|Projet[]
      */
     public function getProjets(): Collection
     {
         return $this->projets;
     }

     public function addProjet(Projet $projet): self
     {
         if (!$this->projets->contains($projet)) {
             $this->projets[] = $projet;
             $projet->setUtilisateur($this);
         }

         return $this;
     }

     public function removeProjet(Projet $projet): self
     {
         if ($this->projets->contains($projet)) {
             $this->projets->removeElement($projet);
             // set the owning side to null (unless already changed)
             if ($projet->getUtilisateur() === $this) {
                 $projet->setUtilisateur(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Ticket[]
      */
     public function getTickets(): Collection
     {
         return $this->tickets;
     }

     public function addTicket(Ticket $ticket): self
     {
         if (!$this->tickets->contains($ticket)) {
             $this->tickets[] = $ticket;
             $ticket->setUtilisateur($this);
         }

         return $this;
     }

     public function removeTicket(Ticket $ticket): self
     {
         if ($this->tickets->contains($ticket)) {
             $this->tickets->removeElement($ticket);
             // set the owning side to null (unless already changed)
             if ($ticket->getUtilisateur() === $this) {
                 $ticket->setUtilisateur(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Ticket[]
      */
     public function getResponsable(): Collection
     {
         return $this->responsable;
     }

     public function addResponsable(Ticket $responsable): self
     {
         if (!$this->responsable->contains($responsable)) {
             $this->responsable[] = $responsable;
             $responsable->setResponsable($this);
         }

         return $this;
     }

     public function removeResponsable(Ticket $responsable): self
     {
         if ($this->responsable->contains($responsable)) {
             $this->responsable->removeElement($responsable);
             // set the owning side to null (unless already changed)
             if ($responsable->getResponsable() === $this) {
                 $responsable->setResponsable(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Projet[]
      */
     public function getChefequipe(): Collection
     {
         return $this->chefequipe;
     }

     public function addChefequipe(Projet $chefequipe): self
     {
         if (!$this->chefequipe->contains($chefequipe)) {
             $this->chefequipe[] = $chefequipe;
             $chefequipe->setResponsable($this);
         }

         return $this;
     }

     public function removeChefequipe(Projet $chefequipe): self
     {
         if ($this->chefequipe->contains($chefequipe)) {
             $this->chefequipe->removeElement($chefequipe);
             // set the owning side to null (unless already changed)
             if ($chefequipe->getResponsable() === $this) {
                 $chefequipe->setResponsable(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Sprint[]
      */
     public function getSprints(): Collection
     {
         return $this->sprints;
     }

     public function addSprint(Sprint $sprint): self
     {
         if (!$this->sprints->contains($sprint)) {
             $this->sprints[] = $sprint;
             $sprint->setUtilisateur($this);
         }

         return $this;
     }

     public function removeSprint(Sprint $sprint): self
     {
         if ($this->sprints->contains($sprint)) {
             $this->sprints->removeElement($sprint);
             // set the owning side to null (unless already changed)
             if ($sprint->getUtilisateur() === $this) {
                 $sprint->setUtilisateur(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Commentaire[]
      */
     public function getCommentaires(): Collection
     {
         return $this->commentaires;
     }

     public function addCommentaire(Commentaire $commentaire): self
     {
         if (!$this->commentaires->contains($commentaire)) {
             $this->commentaires[] = $commentaire;
             $commentaire->setUtilisateur($this);
         }

         return $this;
     }

     public function removeCommentaire(Commentaire $commentaire): self
     {
         if ($this->commentaires->contains($commentaire)) {
             $this->commentaires->removeElement($commentaire);
             // set the owning side to null (unless already changed)
             if ($commentaire->getUtilisateur() === $this) {
                 $commentaire->setUtilisateur(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Contact[]
      */
     public function getExpediteurs(): Collection
     {
         return $this->expediteurs;
     }

     public function addExpediteur(Contact $expediteur): self
     {
         if (!$this->expediteurs->contains($expediteur)) {
             $this->expediteurs[] = $expediteur;
             $expediteur->setExpediteur($this);
         }

         return $this;
     }

     public function removeExpediteur(Contact $expediteur): self
     {
         if ($this->expediteurs->contains($expediteur)) {
             $this->expediteurs->removeElement($expediteur);
             // set the owning side to null (unless already changed)
             if ($expediteur->getExpediteur() === $this) {
                 $expediteur->setExpediteur(null);
             }
         }

         return $this;
     }

     /**
      * @return Collection|Contact[]
      */
     public function getDestinataires(): Collection
     {
         return $this->destinataires;
     }

     public function addDestinataire(Contact $destinataire): self
     {
         if (!$this->destinataires->contains($destinataire)) {
             $this->destinataires[] = $destinataire;
             $destinataire->setDestinataire($this);
         }

         return $this;
     }

     public function removeDestinataire(Contact $destinataire): self
     {
         if ($this->destinataires->contains($destinataire)) {
             $this->destinataires->removeElement($destinataire);
             // set the owning side to null (unless already changed)
             if ($destinataire->getDestinataire() === $this) {
                 $destinataire->setDestinataire(null);
             }
         }

         return $this;
     }

     public function getProjet(): ?Projet
     {
         return $this->projet;
     }

     public function setProjet(?Projet $projet): self
     {
         $this->projet = $projet;

         return $this;
     }


}
