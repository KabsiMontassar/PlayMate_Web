<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Terrain
 *
 * @ORM\Table(name="terrain", indexes={@ORM\Index(name="fk_avis_prop", columns={"idprop"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TerrainRepository")
 */
class Terrain
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
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @var bool
     *
     * @ORM\Column(name="gradin", type="boolean", nullable=false)
     */
    private $gradin;

    /**
     * @var bool
     *
     * @ORM\Column(name="vestiaire", type="boolean", nullable=false)
     */
    private $vestiaire;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="nomTerrain", type="string", length=255, nullable=false)
     */
    private $nomterrain;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=false)
     */
    private $prix;

    /**
     * @var int
     *
     * @ORM\Column(name="duree", type="integer", nullable=false)
     */
    private $duree;

    /**
     * @var string
     *
     * @ORM\Column(name="gouvernorat", type="string", length=255, nullable=false)
     */
    private $gouvernorat;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="video", type="string", length=255, nullable=false)
     */
    private $video;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idprop", referencedColumnName="id")
     * })
     */
    private $idprop;

    public function getId(): ?int
    {
        return $this->id;
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

    public function isGradin(): ?bool
    {
        return $this->gradin;
    }

    public function setGradin(bool $gradin): static
    {
        $this->gradin = $gradin;

        return $this;
    }

    public function isVestiaire(): ?bool
    {
        return $this->vestiaire;
    }

    public function setVestiaire(bool $vestiaire): static
    {
        $this->vestiaire = $vestiaire;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNomterrain(): ?string
    {
        return $this->nomterrain;
    }

    public function setNomterrain(string $nomterrain): static
    {
        $this->nomterrain = $nomterrain;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getGouvernorat(): ?string
    {
        return $this->gouvernorat;
    }

    public function setGouvernorat(string $gouvernorat): static
    {
        $this->gouvernorat = $gouvernorat;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function getIdprop(): ?User
    {
        return $this->idprop;
    }

    public function setIdprop(?User $idprop): static
    {
        $this->idprop = $idprop;

        return $this;
    }


    public function __toString(): string
    {
        return $this->nomterrain;
    }
}
