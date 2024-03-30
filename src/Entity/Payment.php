<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment", indexes={@ORM\Index(name="idReservation", columns={"idReservation"}), @ORM\Index(name="fk_payment_membre", columns={"idMembre"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
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
     * @var string
     *
     * @ORM\Column(name="datePayment", type="string", length=255, nullable=false)
     */
    private $datepayment;

    /**
     * @var string
     *
     * @ORM\Column(name="horairePayment", type="string", length=255, nullable=false)
     */
    private $horairepayment;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="Payed", type="boolean", nullable=true, options={"default"="NULL"})
     */
    private $payed = 'NULL';

    /**
     * @var \Reservation
     *
     * @ORM\ManyToOne(targetEntity="Reservation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idReservation", referencedColumnName="idReservation")
     * })
     */
    private $idreservation;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMembre", referencedColumnName="id")
     * })
     */
    private $idmembre;

    public function getIdpayment(): ?int
    {
        return $this->idpayment;
    }

    public function getDatepayment(): ?string
    {
        return $this->datepayment;
    }

    public function setDatepayment(string $datepayment): static
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

    public function getIdreservation(): ?Reservation
    {
        return $this->idreservation;
    }

    public function setIdreservation(?Reservation $idreservation): static
    {
        $this->idreservation = $idreservation;

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


}
