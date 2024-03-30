<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Tournoi
 *
 * @ORM\Table(name="tournoi", indexes={@ORM\Index(name="fk_avis_organisateur", columns={"idOrganisateur"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TournoiRepository")

 */
class Tournoi
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="NbmaxEquipe", type="integer", nullable=false)
     */
    private $nbmaxequipe;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="affiche", type="string", length=255, nullable=false)
     */
    private $affiche;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="datedebut", type="string", length=255, nullable=false)
     */
    private $datedebut;

    /**
     * @var string
     *
     * @ORM\Column(name="datefin", type="string", length=255, nullable=false)
     */
    private $datefin;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idOrganisateur", referencedColumnName="id")
     * })
     */
    private $idorganisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbmaxequipe(): ?int
    {
        return $this->nbmaxequipe;
    }

    public function setNbmaxequipe(int $nbmaxequipe): static
    {
        $this->nbmaxequipe = $nbmaxequipe;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAffiche(): ?string
    {
        return $this->affiche;
    }

    public function setAffiche(string $affiche): static
    {
        $this->affiche = $affiche;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getDatedebut(): ?string
    {
        return $this->datedebut;
    }

    public function setDatedebut(string $datedebut): static
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?string
    {
        return $this->datefin;
    }

    public function setDatefin(string $datefin): static
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getIdorganisateur(): ?User
    {
        return $this->idorganisateur;
    }

    public function setIdorganisateur(?User $idorganisateur): static
    {
        $this->idorganisateur = $idorganisateur;

        return $this;
    }


}
