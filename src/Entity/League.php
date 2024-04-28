<?php
// src/Entity/League.php

namespace App\Entity;

class League
{
    private $id;
    private $name;
    private $country;
    private $logo; // Base URL for images is https://lsm-static-prod.livescore.com/medium
    // Ajoutez d'autres propriétés si nécessaire

    // Getter pour l'id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Setter pour l'id
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    // Getter pour le nom
    public function getName(): ?string
    {
        return $this->name;
    }

    // Setter pour le nom
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // Getter pour le pays
    public function getCountry(): ?string
    {
        return $this->country;
    }

    // Setter pour le pays
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    // Getter pour le logo
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    // Setter pour le logo
    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    // Ajoutez d'autres getters et setters si nécessaire
}
