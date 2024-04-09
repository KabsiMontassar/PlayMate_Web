<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation", indexes={@ORM\Index(name="idTerrain", columns={"idTerrain"})})
 * @ORM\Entity
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
     * @ORM\Column(type="date", nullable=false)
     * @Assert\GreaterThan("today")
     */
    private $datereservation;

    /**
     * @var string
     *
     * @ORM\Column(name="heureReservation", type="string", length=255, nullable=false)
     * @Assert\Regex(
     *     pattern="/^([01][0-9]|2[0-3]):[0-5][0-9]$/",
     *     message="L'heure doit être au format HH:MM"
     * )
     * @Assert\GreaterThan(
     *     value="07:59",
     *     message="L'heure doit être après 07:59"
     * )
     * @Assert\LessThanOrEqual(
     *     value="23:59",
     *     message="L'heure doit être avant 00:00"
     * )
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
     * @ORM\JoinColumn(name="idTerrain", referencedColumnName="id")
     * })
     */
    private $idterrain;

    public function __construct()
    {
        $this->isconfirm = false;
    }

    public function getIdreservation(): ?int
    {
        return $this->idreservation;
    }

    public function isIsconfirm(): ?bool
    {
        return $this->isconfirm;
    }

    public function setIsconfirm(bool $isconfirm): self
    {
        $this->isconfirm = $isconfirm;

        return $this;
    }

    public function getDatereservation(): ?\DateTimeInterface
    {
        return $this->datereservation;
    }

    public function setDatereservation(\DateTimeInterface $datereservation): self
    {
        $this->datereservation = $datereservation;

        return $this;
    }

    public function getHeurereservation(): ?string
    {
        return $this->heurereservation;
    }

    public function setHeurereservation(string $heurereservation): self
    {
        $this->heurereservation = $heurereservation;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIdterrain(): ?Terrain
    {
        return $this->idterrain;
    }

    public function setIdterrain(?Terrain $idterrain): self
    {
        $this->idterrain = $idterrain;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->idreservation;
    }
}
