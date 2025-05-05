<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class PollOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;
    
    #[ORM\ManyToOne(targetEntity: Poll::class, inversedBy: 'poll_options')]
    #[ORM\JoinColumn(name: 'poll_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Poll $poll_id = null;
    
    #[ORM\Column(type: 'string', length: 255)]
    private string $text;
    
    #[ORM\Column(type: 'integer')]
    private int $vote_count = 0;
    
    #[ORM\OneToMany(mappedBy: 'option_id', targetEntity: PollVote::class, cascade: ['persist', 'remove'])]
    private Collection $poll_votes;
    
    public function __construct()
    {
        $this->poll_votes = new ArrayCollection();
        $this->vote_count = 0;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $value): self
    {
        $this->id = $value;
        return $this;
    }
    
    public function getPoll_id(): ?Poll
    {
        return $this->poll_id;
    }
    
    public function setPoll_id(?Poll $value): self
    {
        $this->poll_id = $value;
        return $this;
    }
    
    public function getText(): ?string
    {
        return $this->text;
    }
    
    public function setText(string $value): self
    {
        $this->text = $value;
        return $this;
    }
    
    public function getVote_count(): int
    {
        return $this->vote_count;
    }
    
    public function setVote_count(int $value): self
    {
        $this->vote_count = $value;
        return $this;
    }
    
    public function getPoll_votes(): Collection
    {
        return $this->poll_votes;
    }
    
    public function addPoll_vote(PollVote $poll_vote): self
    {
        if (!$this->poll_votes->contains($poll_vote)) {
            $this->poll_votes->add($poll_vote);
            $poll_vote->setOption_id($this);
        }
        return $this;
    }
    
    public function removePoll_vote(PollVote $poll_vote): self
    {
        if ($this->poll_votes->removeElement($poll_vote)) {
            if ($poll_vote->getOption_id() === $this) {
                $poll_vote->setOption_id(null);
            }
        }
        return $this;
    }
}