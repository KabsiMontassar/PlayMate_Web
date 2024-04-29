<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Equipe
 *
 * @ORM\Table(name="equipe")
 * @ORM\Entity(repositoryClass="App\Repository\EquipeRepository")
 */
class Equipe
{
    /**
     * @var int
     *
     * @ORM\Column(name="idEquipe", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idequipe;

    /**
     * @var string
     *
     * @ORM\Column(name="nomEquipe", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank(
     *  message = "The name cannot be blank.",
     * )
     * @Assert\Length(
     * min = 6,
     * minMessage = "The name must be at least {{ limit }} characters long",
     * max = 8,
     * maxMessage = "The name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     * pattern = "/^[a-zA-Z0-9]+$/",
     * message = "The name can only contain letters and numbers"
     * )
     */
    private $nomequipe;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nbreJoueur", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $nbrejoueur = NULL;

    public function getIdequipe(): ?int
    {
        return $this->idequipe;
    }

    public function getNomequipe(): ?string
    {
        return $this->nomequipe;
    }

    public function setNomequipe(string $nomequipe): static
    {
        $this->nomequipe = $nomequipe;

        return $this;
    }

    public function getNbrejoueur(): ?int
    {
        return $this->nbrejoueur;
    }

    public function setNbrejoueur(?int $nbrejoueur): static
    {
        $this->nbrejoueur = $nbrejoueur;

        return $this;
    }

    public function __toString()
    {
        return $this->nomequipe;
    }

}
