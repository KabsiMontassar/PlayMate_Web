<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tournoi
 *
 * @ORM\Table(name="tournoi", indexes={@ORM\Index(name="fk_avis_organisateur", columns={"idOrganisateur"})})
 * @ORM\Entity
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
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9 ]+$/",
     *     message="Le nom ne doit contenir que des lettres, des chiffres et des espaces."
     * )
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
    * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9 ]+$/",
     *     message="L'adresse ne doit contenir que des lettres, des chiffres et des espaces."
     * )
     */
    private $address;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datedebut", type="date", nullable=false)
     * @Assert\LessThanOrEqual(
     *     propertyPath="datefin",
     *     message="La date de début doit être antérieure ou égale à la date de fin."
     * )
     */
    private $datedebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datefin", type="date", nullable=false)
     * @Assert\Expression(
     *     "this.getDatedebut() <= this.getDatefin()",
     *     message="La date de fin doit être postérieure à la date de début."
     * )
     */
    private $datefin;

    /**
     * @var int
     *
     * @ORM\Column(name="visite", type="integer", nullable=false)
     */
    private $visite = '0';

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

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): static
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): static
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getVisite(): ?int
    {
        return $this->visite;
    }

    public function setVisite(int $visite): static
    {
        $this->visite = $visite;

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

    public function __toString()
    {
        return $this->nom;
    }



}
