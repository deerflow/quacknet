<?php

namespace App\Entity;

use App\Repository\QuackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuackRepository::class)
 */
class Quack
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Duck::class, inversedBy="quacks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Tag::class, mappedBy="quack_id", orphanRemoval=true)
     */
    private $hashtags;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="quack", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->hashtags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?Duck
    {
        return $this->author;
    }

    public function setAuthor(Duck $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getHashtags(): Collection
    {
        return $this->hashtags;
    }

    public function addHashtag(Tag $hashtag): self
    {
        if (!$this->hashtags->contains($hashtag)) {
            $this->hashtags[] = $hashtag;
            $hashtag->setQuackId($this);
        }

        return $this;
    }

    public function removeHashtag(Tag $hashtag): self
    {
        if ($this->hashtags->removeElement($hashtag)) {
            // set the owning side to null (unless already changed)
            if ($hashtag->getQuackId() === $this) {
                $hashtag->setQuackId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setQuack($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getQuack() === $this) {
                $comment->setQuack(null);
            }
        }

        return $this;
    }
}
