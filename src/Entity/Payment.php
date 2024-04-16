<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payment
 *
 * @ORM\Table(name="payment", indexes={@ORM\Index(name="idReservation", columns={"idReservation"}), @ORM\Index(name="fk_payment_membre", columns={"idMembre"})})
 * @ORM\Entity
 */
class Payment
{
    /**
     * @var int
     *
     * @ORM\Column(name="idPayment", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idpayment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     */
    private $datepayment;

    /**
     * @var string
     *
     * @ORM\Column(name="horairePayment", type="string", length=255, nullable=false)
     * @Assert\Regex(
     *     pattern="/^([01][0-9]|2[0-3]):[0-5][0-9]$/",
     *     message="L'heure doit être au format HH:MM"
     * )
     */
    private $horairepayment;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="Payed", type="boolean", nullable=true, options={"default"="NULL"})
     * 
     */
    private $payed = 'NULL';

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMembre", referencedColumnName="id")
     * })
     */
    private $idmembre;

    /**
     * @var \Reservation
     *
     * @ORM\ManyToOne(targetEntity="Reservation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idReservation", referencedColumnName="idReservation")
     * })
     */
    private $idreservation;

    public function getIdpayment(): ?int
    {
        return $this->idpayment;
    }

    public function getDatepayment(): ?\DateTime
    {
        return $this->datepayment;
    }

    public function setDatepayment(\DateTime $datepayment): static
    {
        $this->datepayment = $datepayment;

        return $this;
    }

    public function getHorairepayment(): ?string
    {
        return $this->horairepayment;
    }

    public function setHorairepayment(string $horairepayment): static
    {
        $this->horairepayment = $horairepayment;

        return $this;
    }

    public function isPayed(): ?bool
    {
        return $this->payed;
    }

    public function setPayed(?bool $payed): static
    {
        $this->payed = $payed;

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
        return $this->horairepayment;
    }
}
