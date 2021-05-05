<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Quack::class, inversedBy="hashtags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quack_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuackId(): ?Quack
    {
        return $this->quack_id;
    }

    public function setQuackId(?Quack $quack_id): self
    {
        $this->quack_id = $quack_id;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function __toString() {
        return $this->getText();
    }
}
