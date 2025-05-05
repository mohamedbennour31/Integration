<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PollVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Poll::class, inversedBy: 'poll_votes')]
    #[ORM\JoinColumn(name: 'poll_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Poll $poll = null;

    #[ORM\ManyToOne(targetEntity: PollOption::class, inversedBy: 'poll_votes')]
    #[ORM\JoinColumn(name: 'option_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?PollOption $option_id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "pollVotes")]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $value): self
    {
        $this->id = $value;
        return $this;
    }

    public function getPoll(): ?Poll
    {
        return $this->poll;
    }

    public function setPoll(?Poll $value): self
    {
        $this->poll = $value;
        return $this;
    }

    public function getOption_id(): ?PollOption
    {
        return $this->option_id;
    }

    public function setOption_id(?PollOption $value): self
    {
        $this->option_id = $value;
        return $this;
    }

    public function getUser_id(): ?User
    {
        return $this->user;
    }

    public function setUser_id(?User $value): self
    {
        $this->user = $value;
        return $this;
    }
}
