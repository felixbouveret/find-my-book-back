<?php

namespace App\Entity;

use App\Repository\NotesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=NotesRepository::class)
 */
class Notes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"show_commentary"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"show_commentary"})
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"show_commentary"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Livres::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"show_commentary"})
     */
    private $livre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLivre(): ?Livres
    {
        return $this->livre;
    }

    public function setLivre(?Livres $livre): self
    {
        $this->livre = $livre;

        return $this;
    }
}
