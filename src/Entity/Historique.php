<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historique
 *
 * @ORM\Table(name="historique")
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
     * @var Reservation|null
     *
     * @ORM\ManyToOne(targetEntity=Reservation::class)
     * @ORM\JoinColumn(name="idReservation", referencedColumnName="idReservation", nullable=false)
     */
    private $reservation;

    public function getIdhistorique(): ?int
    {
        return $this->idhistorique;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;
        return $this;
    }
}
