<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Chapitres;

#[ORM\Entity]
class Ressources
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "string", length: 255)]
    private string $type;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_ajout;

    #[ORM\Column(type: "boolean")]
    private bool $valide;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($value)
    {
        $this->titre = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getDate_ajout()
    {
        return $this->date_ajout;
    }

    public function setDate_ajout($value)
    {
        $this->date_ajout = $value;
    }

    public function getValide()
    {
        return $this->valide;
    }

    public function setValide($value)
    {
        $this->valide = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_ressources", targetEntity: Chapitres::class)]
    private Collection $chapitres;

    public function getChapitress(): Collection
    {
        return $this->chapitres;
    }

    public function addChapitres(Chapitres $chapitres): self
    {
        if (!$this->chapitres->contains($chapitres)) {
            $this->chapitres[] = $chapitres;
            $chapitres->setId_ressources($this);
        }

        return $this;
    }

    public function removeChapitres(Chapitres $chapitres): self
    {
        if ($this->chapitres->removeElement($chapitres)) {
            // set the owning side to null (unless already changed)
            if ($chapitres->getId_ressources() === $this) {
                $chapitres->setId_ressources(null);
            }
        }

        return $this;
    }
}
