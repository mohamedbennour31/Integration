<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Vote;

#[ORM\Entity]
class Projets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;




    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "string", length: 255)]
    private string $statut;

    #[ORM\Column(type: "string", length: 255)]
    private string $priorite;

    #[ORM\Column(type: "string", length: 255)]
    private string $description;

    #[ORM\Column(type: "string", length: 255)]
    private string $ressource;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }



    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }

    public function getPriorite()
    {
        return $this->priorite;
    }

    public function setPriorite($value)
    {
        $this->priorite = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getRessource()
    {
        return $this->ressource;
    }

    public function setRessource($value)
    {
        $this->ressource = $value;
    }





    #[ORM\OneToMany(mappedBy: "idProjet", targetEntity: Vote::class)]
    private Collection $votes;

    /**
     * @var Collection<int, Technologies>
     */
    #[ORM\ManyToMany(targetEntity: Technologies::class, inversedBy: 'projets')]
    private Collection $technologies;

    #[ORM\ManyToOne(targetEntity: Hackathon::class, inversedBy: "projets")]
    #[ORM\JoinColumn(name: "id_hackathon", referencedColumnName: "id")]
    private ?Hackathon $hackathon = null;




    public function __construct()
    {
        $this->technologies = new ArrayCollection();
    }

    /**
     * @return Collection<int, Technologies>
     */
    public function getTechnologies(): Collection
    {
        return $this->technologies;
    }

    public function addTechnology(Technologies $technology): static
    {
        if (!$this->technologies->contains($technology)) {
            $this->technologies->add($technology);
        }

        return $this;
    }

    public function removeTechnology(Technologies $technology): static
    {
        $this->technologies->removeElement($technology);

        return $this;
    }

    public function getIdHack(): ?Hackathon
    {
        return $this->hackathon;
    }

    public function setIdHack(?Hackathon $id_hack): static
    {
        $this->hackathon = $id_hack;

        return $this;
    }
}
