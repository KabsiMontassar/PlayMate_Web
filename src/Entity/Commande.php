<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande", indexes={@ORM\Index(name="commande_ibfk_1", columns={"idproduit"}), @ORM\Index(name="fk_avis_ods", columns={"idmembre"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
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
     * @var string
     *
     * @ORM\Column(name="DateCommande", type="string", length=255, nullable=false)
     */
    private $datecommande;

    /**
     * @var \Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idproduit", referencedColumnName="id")
     * })
     */
    private $idproduit;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idmembre", referencedColumnName="id")
     * })
     */
    private $idmembre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatecommande(): ?string
    {
        return $this->datecommande;
    }

    public function setDatecommande(string $datecommande): static
    {
        $this->datecommande = $datecommande;

        return $this;
    }

    public function getIdproduit(): ?Product
    {
        return $this->idproduit;
    }

    public function setIdproduit(?Product $idproduit): static
    {
        $this->idproduit = $idproduit;

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
