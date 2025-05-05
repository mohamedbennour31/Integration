<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Communaute;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Message;

#[ORM\Entity]
class Chat
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Communaute::class, inversedBy: "chats")]
    #[ORM\JoinColumn(name: 'communaute_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Communaute $communaute_id;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "string")]
    private string $type;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_creation;

    #[ORM\Column(type: "boolean")]
    private bool $is_active;

    #[ORM\OneToMany(mappedBy: "chat_id", targetEntity: Poll::class)]
    private Collection $polls;

    #[ORM\OneToMany(mappedBy: "chat_id", targetEntity: Message::class)]
    private Collection $messages;

    public function __construct()
    {
        $this->polls = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->is_active = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommunaute_id()
    {
        return $this->communaute_id;
    }

    public function setCommunaute_id($value)
    {
        $this->communaute_id = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;
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

    public function getPolls(): Collection
    {
        return $this->polls;
    }

    public function addPoll(Poll $poll): self
    {
        if (!$this->polls->contains($poll)) {
            $this->polls[] = $poll;
            $poll->setChat_id($this);
        }

        return $this;
    }

    public function removePoll(Poll $poll): self
    {
        if ($this->polls->removeElement($poll)) {
            // set the owning side to null (unless already changed)
            if ($poll->getChat_id() === $this) {
                $poll->setChat_id(null);
            }
        }

        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setChat_id($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChat_id() === $this) {
                $message->setChat_id(null);
            }
        }

        return $this;
    }
}
