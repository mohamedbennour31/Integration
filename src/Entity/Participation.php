<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ParticipationRepository;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
#[ORM\Table(name: 'participation')]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_participation = null;

    public function getId_participation(): ?int
    {
        return $this->id_participation;
    }

    public function setId_participation(int $id_participation): self
    {
        $this->id_participation = $id_participation;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Hackathon::class, inversedBy: 'participations')]
    #[ORM\JoinColumn(name: 'id_hackathon', referencedColumnName: 'id')]
    private ?Hackathon $hackathon = null;

    public function getHackathon(): ?Hackathon
    {
        return $this->hackathon;
    }

    public function setHackathon(?Hackathon $hackathon): self
    {
        $this->hackathon = $hackathon;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "participations")]
    #[ORM\JoinColumn(name: 'id_participant', referencedColumnName: 'id')]
    private ?User $id_participant = null;

    public function getParticipant(): ?User
    {
        return $this->id_participant;
    }

    public function setParticipant(?User $participant): self
    {
        $this->id_participant = $participant;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_inscription = null;

    public function getDate_inscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDate_inscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getIdParticipation(): ?int
    {
        return $this->id_participation;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): static
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }
}
