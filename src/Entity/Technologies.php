<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Technologies
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom_tech;

    #[ORM\Column(type: "string", length: 255)]
    private string $type_tech;

    #[ORM\Column(type: "string", length: 255)]
    private string $complexite;

    #[ORM\Column(type: "string", length: 255)]
    private string $documentaire;

    #[ORM\Column(type: "string", length: 255)]
    private string $compatibilite;

    /**
     * @var Collection<int, Projets>
     */
    #[ORM\ManyToMany(targetEntity: Projets::class, mappedBy: 'technologies')]
    private Collection $projets;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getNomTech()
    {
        return $this->nom_tech;
    }

    public function setNomTech($value)
    {
        $this->nom_tech = $value;
    }

    public function getType_tech()
    {
        return $this->type_tech;
    }

    public function setType_tech($value)
    {
        $this->type_tech = $value;
    }

    public function getComplexite()
    {
        return $this->complexite;
    }

    public function setComplexite($value)
    {
        $this->complexite = $value;
    }

    public function getDocumentaire()
    {
        return $this->documentaire;
    }

    public function setDocumentaire($value)
    {
        $this->documentaire = $value;
    }

    public function getCompatibilite()
    {
        return $this->compatibilite;
    }

    public function setCompatibilite($value)
    {
        $this->compatibilite = $value;
    }

    /**
     * @return Collection<int, Projets>
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projets $projet): static
    {
        if (!$this->projets->contains($projet)) {
            $this->projets->add($projet);
            $projet->addTechnology($this);
        }

        return $this;
    }

    public function removeProjet(Projets $projet): static
    {
        if ($this->projets->removeElement($projet)) {
            $projet->removeTechnology($this);
        }

        return $this;
    }


      
    
    
}
