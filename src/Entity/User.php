<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Vote;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    private string $nomUser;



    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $emailUser;

    #[ORM\Column(type: "json")]
    private array $roleUser = [];

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 20)]
    private int $telUser;

    #[ORM\Column(type: "string", length: 255)]
    private string $mdpUser;

    #[ORM\Column(type: "string", length: 255)]
    private string $adresseUser;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photoUser = null;

    #[ORM\Column(type: "string", length: 20)]
    private string $statusUser;

    #[ORM\OneToMany(mappedBy: "id_organisateur", targetEntity: Hackathon::class)]
    private Collection $hackathons;

    #[ORM\OneToMany(mappedBy: "posted_by", targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: PollVote::class)]
    private Collection $pollVotes;

    #[ORM\OneToMany(mappedBy: "idVotant", targetEntity: Vote::class)]
    private Collection $votes;

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setPosted_by($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getPosted_by() === $this) {
                $message->setPosted_by(null);
            }
        }
        return $this;
    }

    #[ORM\OneToMany(mappedBy: "id_participant", targetEntity: Participation::class)]
    private Collection $participations;

    #[ORM\Column(length: 255)]
    private ?string $prenomUser = null;

    public function __construct()
    {
        $this->hackathons = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $idUser): self
    {
        $this->id = $idUser;
        return $this;
    }

    public function getNomUser(): ?string
    {
        return $this->nomUser;
    }

    public function setNomUser(string $nomUser): self
    {
        $this->nomUser = $nomUser;
        return $this;
    }

    public function getEmailUser(): ?string
    {
        return $this->emailUser;
    }

    public function setEmailUser(string $emailUser): self
    {
        $this->emailUser = $emailUser;
        return $this;
    }

    public function getRoleUser(): array
    {
        return $this->roleUser;
    }

    public function setRoleUser(array $roleUser): self
    {
        $this->roleUser = $roleUser;
        return $this;
    }

    public function getTelUser(): ?string
    {
        return $this->telUser;
    }

    public function setTelUser(string $telUser): self
    {
        $this->telUser = $telUser;
        return $this;
    }

    public function getMdpUser(): ?string
    {
        return $this->mdpUser;
    }

    public function setMdpUser(string $mdpUser): self
    {
        $this->mdpUser = $mdpUser;
        return $this;
    }

    public function getAdresseUser(): ?string
    {
        return $this->adresseUser;
    }

    public function setAdresseUser(string $adresseUser): self
    {
        $this->adresseUser = $adresseUser;
        return $this;
    }

    public function getPhotoUser(): ?string
    {
        return $this->photoUser;
    }

    public function setPhotoUser(?string $photoUser): self
    {
        $this->photoUser = $photoUser;
        return $this;
    }

    public function getStatusUser(): ?string
    {
        return $this->statusUser;
    }

    public function setStatusUser(string $statusUser): self
    {
        $this->statusUser = $statusUser;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roleUser;

        // Ensure $roles is an array even if stored differently
        if (!is_array($roles)) {
            // If it's a JSON string, try to decode it
            if (is_string($roles) && !empty($roles)) {
                $decodedRoles = json_decode($roles, true);
                $roles = is_array($decodedRoles) ? $decodedRoles : [];
            } else {
                $roles = [];
            }
        }

        // Make sure user always has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roleUser = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->mdpUser;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->emailUser;
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setParticipant($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getParticipant() === $this) {
                $participation->setParticipant(null);
            }
        }

        return $this;
    }

    public function getPrenomUser(): ?string
    {
        return $this->prenomUser;
    }

    public function setPrenomUser(string $prenomUser): static
    {
        $this->prenomUser = $prenomUser;

        return $this;
    }
    public function __toString(): string
    {
        return (string) $this->getId(); // Replace getName() with any property you want to show
    }
}
