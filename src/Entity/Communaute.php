<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Hackathon;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Chat;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Communaute
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Hackathon::class, inversedBy: "communautes")]
    #[ORM\JoinColumn(name: 'id_hackathon', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Hackathon $id_hackathon;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le nom de la communauté est requis.')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le nom doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $nom;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank(message: 'La description est requise.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.'
    )]
    private string $description;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_creation;

    #[ORM\Column(type: "boolean")]
    private bool $is_active;

    #[ORM\OneToMany(mappedBy: "communaute_id", targetEntity: Chat::class)]
    private Collection $chats;

    public function __construct()
    {
        $this->chats = new ArrayCollection();
        $this->is_active = true;
        $this->date_creation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getId_hackathon()
    {
        return $this->id_hackathon;
    }

    public function setId_hackathon($value)
    {
        $this->id_hackathon = $value;
    }

    public function getIdHackathon()
    {
        return $this->id_hackathon;
    }

    public function setIdHackathon($value)
    {
        $this->id_hackathon = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getDate_creation()
    {
        return $this->date_creation;
    }

    public function setDate_creation($value)
    {
        $this->date_creation = $value;
    }

    public function getIs_active()
    {
        return $this->is_active;
    }

    public function setIs_active($value)
    {
        $this->is_active = $value;
    }

    // Camelcase accessors for Symfony form compatibility
    public function getIsActive()
    {
        return $this->is_active;
    }

    public function setIsActive($value)
    {
        $this->is_active = $value;
    }

    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->setCommunaute_id($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->removeElement($chat)) {
            // Set the owning side to null (unless already changed)
            if ($chat->getCommunaute_id() === $this) {
                $chat->setCommunaute_id(null);
            }
        }

        return $this;
    }
}
