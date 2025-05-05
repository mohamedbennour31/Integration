<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]  // Added for auto-increment
    #[ORM\Column(type: 'integer')]  // Removed name attribute to default to 'id'
    private ?int $id = null;

    #[ORM\Column(name: 'idHackathon', type: 'integer')]
    private ?int $idHackathon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // Removed setId() as IDs are typically auto-generated
    // public function setId(int $id): static
    // {
    //     $this->id = $id;
    //     return $this;
    // }

    public function getIdHackathon(): ?int
    {
        return $this->idHackathon;
    }

    public function setIdHackathon(int $idHackathon): static
    {
        $this->idHackathon = $idHackathon;
        return $this;
    }
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
