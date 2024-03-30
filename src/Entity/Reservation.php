<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation", indexes={@ORM\Index(name="idTerrain", columns={"idTerrain"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @var int
     *
     * @ORM\Column(name="idReservation", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idreservation;

    /**
     * @var bool
     *
     * @ORM\Column(name="isConfirm", type="boolean", nullable=false)
     */
    private $isconfirm;

    /**
     * @var string
     *
     * @ORM\Column(name="dateReservation", type="string", length=255, nullable=false)
     */
    private $datereservation;

    /**
     * @var string
     *
     * @ORM\Column(name="heureReservation", type="string", length=255, nullable=false)
     */
    private $heurereservation;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

   

   

    /**
     * @var \Terrain
     *
     * @ORM\ManyToOne(targetEntity="Terrain")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTerrain", referencedColumnName="id")
     * })
     */
    private $idterrain;

    public function getIdreservation(): ?int
    {
        return $this->idreservation;
    }

    public function isIsconfirm(): ?bool
    {
        return $this->isconfirm;
    }

    public function setIsconfirm(bool $isconfirm): static
    {
        $this->isconfirm = $isconfirm;

        return $this;
    }

    public function getDatereservation(): ?string
    {
        return $this->datereservation;
    }

    public function setDatereservation(string $datereservation): static
    {
        $this->datereservation = $datereservation;

        return $this;
    }

    public function getHeurereservation(): ?string
    {
        return $this->heurereservation;
    }

    public function setHeurereservation(string $heurereservation): static
    {
        $this->heurereservation = $heurereservation;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

 
    public function getIdterrain(): ?Terrain
    {
        return $this->idterrain;
    }

    public function setIdterrain(?Terrain $idterrain): static
    {
        $this->idterrain = $idterrain;

        return $this;
    }


}
