<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Membreparequipe
 *
 * @ORM\Table(name="membreparequipe", indexes={@ORM\Index(name="idEquipe", columns={"idEquipe"}), @ORM\Index(name="fk_abaa", columns={"idMembre"})})
 * @ORM\Entity
 */
class Membreparequipe
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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMembre", referencedColumnName="id")
     * })
     */
    private $idmembre;

    /**
     * @var \Equipe
     *
     * @ORM\ManyToOne(targetEntity="Equipe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idEquipe", referencedColumnName="idEquipe")
     * })
     */
    private $idequipe;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdequipe(): ?Equipe
    {
        return $this->idequipe;
    }

    public function setIdequipe(?Equipe $idequipe): static
    {
        $this->idequipe = $idequipe;

        return $this;
    }


}
