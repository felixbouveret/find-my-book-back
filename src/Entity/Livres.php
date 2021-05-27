<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LivresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=LivresRepository::class)
 */
class Livres
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"show_commentary", "show_notes", "show_likes"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_commentary", "show_notes", "show_likes"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_commentary", "show_notes", "show_likes"})
     */
    private $isbn_code;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"show_commentary", "show_notes", "show_likes"})
     */
    private $synopsis;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_commentary", "show_notes", "show_likes"})
     */
    private $auteur;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="likes")
     * @Groups({"show_likes"})
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Notes::class, mappedBy="livre", orphanRemoval=true)
     * @Groups({"show_notes"})
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="livre", orphanRemoval=true)
     * @Groups({"show_commentary"})
     * @MaxDepth(2)
     */
    private $commentaires;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"show_commentary", "show_notes", "show_likes"})
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show_commentary", "show_notes", "show_likes"})
     */
    private $img_url;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsbnCode(): ?string
    {
        return $this->isbn_code;
    }

    public function setIsbnCode(string $isbn_code): self
    {
        $this->isbn_code = $isbn_code;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->addLike($this);
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        if ($this->likes->removeElement($like)) {
            $like->removeLike($this);
        }

        return $this;
    }

    /**
     * @return Collection|Notes[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Notes $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setLivre($this);
        }

        return $this;
    }

    public function removeNote(Notes $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getLivre() === $this) {
                $note->setLivre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentaires[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setLivre($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getLivre() === $this) {
                $commentaire->setLivre(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->img_url;
    }

    public function setImgUrl(?string $img_url): self
    {
        $this->img_url = $img_url;

        return $this;
    }
}
