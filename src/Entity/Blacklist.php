<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blacklist
 *
 * @ORM\Table(name="blacklist", indexes={@ORM\Index(name="idReservation", columns={"idReservation"})})
 * @ORM\Entity
 */
class Blacklist
{
    /**
     * @var int
     *
     * @ORM\Column(name="idBlackList", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idblacklist;

    /**
     * @var int
     *
     * @ORM\Column(name="duree", type="integer", nullable=false)
     */
    private $duree;

    /**
     * @var string
     *
     * @ORM\Column(name="cause", type="string", length=255, nullable=false)
     */
    private $cause;

    /**
     * @var \Reservation
     *
     * @ORM\ManyToOne(targetEntity="Reservation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idReservation", referencedColumnName="idReservation")
     * })
     */
    private $idreservation;

    public function getIdblacklist(): ?int
    {
        return $this->idblacklist;
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

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(string $cause): static
    {
        $this->cause = $cause;

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

    public function __toString()
    {
        return $this->cause;
    }
}
