<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Historique
 *
 * @ORM\Table(name="historique", indexes={@ORM\Index(name="idReservation", columns={"idReservation"})})
 * @ORM\Entity
 */
class Historique
{
    /**
     * @var int
     *
     * @ORM\Column(name="idHistorique", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idhistorique;

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
     * @var \Reservation
     *
     * @ORM\ManyToOne(targetEntity="Reservation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idReservation", referencedColumnName="idReservation")
     * })
     */
    private $idreservation;

    public function getIdhistorique(): ?int
    {
        return $this->idhistorique;
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

    public function getIdreservation(): ?Reservation
    {
        return $this->idreservation;
    }

    public function setIdreservation(?Reservation $idreservation): static
    {
        $this->idreservation = $idreservation;

        return $this;
    }


}
