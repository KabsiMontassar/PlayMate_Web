<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participation
 *
 * @ORM\Table(name="participation", indexes={@ORM\Index(name="fk_adsds", columns={"idmembre"}), @ORM\Index(name="idTournoi", columns={"idTournoi"})})
 * @ORM\Entity
 */
class Participation
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
     * @var bool
     *
     * @ORM\Column(name="Status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="datec", type="string", length=255, nullable=false)
     */
    private $datec;

    /**
     * @var string
     *
     * @ORM\Column(name="NomEquipe", type="string", length=255, nullable=false)
     */
    private $nomequipe;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idmembre", referencedColumnName="id")
     * })
     */
    private $idmembre;

    /**
     * @var \Tournoi
     *
     * @ORM\ManyToOne(targetEntity="Tournoi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTournoi", referencedColumnName="id")
     * })
     */
    private $idtournoi;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDatec(): ?string
    {
        return $this->datec;
    }

    public function setDatec(string $datec): static
    {
        $this->datec = $datec;

        return $this;
    }

    public function getNomequipe(): ?string
    {
        return $this->nomequipe;
    }

    public function setNomequipe(string $nomequipe): static
    {
        $this->nomequipe = $nomequipe;

        return $this;
    }

    public function getIdmembre(): ?User
    {
        return $this->idmembre;
    }

    public function setIdmembre(?User $idmembre): static
    {
        $this->idmembre = $idmembre;

        return $this;
    }

    public function getIdtournoi(): ?Tournoi
    {
        return $this->idtournoi;
    }

    public function setIdtournoi(?Tournoi $idtournoi): static
    {
        $this->idtournoi = $idtournoi;

        return $this;
    }


}
