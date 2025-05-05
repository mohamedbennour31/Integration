<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Evaluation;
use App\Entity\Hackathon;
use App\Entity\Projet;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Evaluation::class, inversedBy: 'votes')]
    #[ORM\JoinColumn(name: 'idEvaluation', referencedColumnName: 'id')]
    #[Assert\NotBlank(message: "Evaluation must not be blank.")]
    private ?Evaluation $idEvaluation = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "votes")]
    #[ORM\JoinColumn(name: "idVotant", referencedColumnName: "id")]
    #[Assert\NotBlank(message: "Voter ID must not be blank.")]
    private ?User $idVotant = null;

    #[ORM\ManyToOne(targetEntity: Projet::class)]
    #[ORM\JoinColumn(name: "idProjet", referencedColumnName: "id")]
    #[Assert\NotBlank(message: "Project must not be blank.")]
    private ?Projet $idProjet = null;

    #[ORM\ManyToOne(targetEntity: Hackathon::class, inversedBy: 'votes')]
    #[ORM\JoinColumn(name: 'idHackathon', referencedColumnName: 'id')]
    #[Assert\NotBlank(message: "Hackathon must not be blank.")]
    private ?Hackathon $idHackathon = null;

    #[ORM\Column(name: 'valeurVote')]
    #[Assert\NotBlank(message: "Vote value must not be blank.")]
    #[Assert\Positive(message: "Vote value must be a positive value.")]
    #[Assert\Type(type: 'numeric', message: "Technical note must be a number.")]
    private ?float $valeurVote = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "Date must not be blank.")]
    private ?\DateTimeInterface $date = null;

    public function __construct()
    {
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

    public function getIdEvaluation(): ?Evaluation
    {
        return $this->idEvaluation;
    }

    public function setIdEvaluation(?Evaluation $idEvaluation): static
    {
        $this->idEvaluation = $idEvaluation;
        return $this;
    }

    public function getIdVotant(): ?User
    {
        return $this->idVotant;
    }

    public function setIdVotant(?User $idVotant): self
    {
        $this->idVotant = $idVotant;
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

    public function getIdHackathon(): ?Hackathon
    {
        return $this->idHackathon;
    }

    public function setIdHackathon(?Hackathon $idHackathon): static
    {
        $this->idHackathon = $idHackathon;
        return $this;
    }

    public function getValeurVote(): ?float
    {
        return $this->valeurVote;
    }

    public function setValeurVote(float $valeurVote): static
    {
        $this->valeurVote = $valeurVote;
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
}
