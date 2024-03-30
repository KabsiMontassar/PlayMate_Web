<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Historique
 *
 * @ORM\Table(name="historique", indexes={@ORM\Index(name="idReservation", columns={"idReservation"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueRepository")
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
