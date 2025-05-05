<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Jury;
use App\Entity\Hackathon;
use App\Entity\Projet;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Jury::class)]
    #[ORM\JoinColumn(name: "idJury", referencedColumnName: "id")]
    #[Assert\NotBlank(message: "Jury must not be blank.")]
    private ?Jury $idJury = null;

    #[ORM\ManyToOne(targetEntity: Hackathon::class, inversedBy: 'evaluations')]
    #[ORM\JoinColumn(name: 'idHackathon', referencedColumnName: 'id')]
    #[Assert\NotBlank(message: "Hackathon must not be blank.")]
    private ?Hackathon $idHackathon = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: "idProjet", referencedColumnName: "id")]
    #[Assert\NotBlank(message: "Project must not be blank.")]
    private ?Projet $idProjet = null;

    #[ORM\Column(name: 'noteTech')]
    #[Assert\NotBlank(message: "Technical note must not be blank.")]
    #[Assert\Positive(message: "Technical note must be a positive value.")]
    #[Assert\Type(type: 'numeric', message: "Technical note must be a number.")]
    private ?float $noteTech = null;

    #[ORM\Column(name: 'noteInnov')]
    #[Assert\NotBlank(message: "Innovation note must not be blank.")]
    #[Assert\Positive(message: "Innovation note must be a positive value.")]
    #[Assert\Type(type: 'numeric', message: "Technical note must be a number.")]
    private ?float $noteInnov = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "Date must not be blank.")]
    #[Assert\EqualTo("today", message: "The date must be today's date.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(mappedBy: 'idEvaluation', targetEntity: Vote::class)]
    #[ORM\JoinColumn(name: 'idVote', referencedColumnName: 'id')]
    private $votes;  // This represents the list of votes related to an evaluation

    public function __construct()
    {
        $this->votes = new ArrayCollection(); // Initialize the votes collection
        $this->date = new \DateTime(); // today's date as default
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getIdJury(): ?Jury
    {
        return $this->idJury;
    }

    public function setIdJury(?Jury $idJury): static
    {
        $this->idJury = $idJury;
        return $this;
    }

    public function getIdHackathon(): ?Hackathon
    {
        return $this->idHackathon;
    }

    public function setIdHackathon(?Hackathon $idHackathon): static
    {
        $this->idHackathon = $idHackathon;
        return $this;
    }

    public function getIdProjet(): ?Projet
    {
        return $this->idProjet;
    }

    public function setIdProjet(?Projet $idProjet): static
    {
        $this->idProjet = $idProjet;
        return $this;
    }

    public function getNoteTech(): ?float
    {
        return $this->noteTech;
    }

    public function setNoteTech(float $noteTech): static
    {
        $this->noteTech = $noteTech;
        return $this;
    }

    public function getNoteInnov(): ?float
    {
        return $this->noteInnov;
    }

    public function setNoteInnov(float $noteInnov): static
    {
        $this->noteInnov = $noteInnov;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function __toString(): string
    {
        return 'Evaluation #' . $this->id ?? 'N/A';
    }
}
