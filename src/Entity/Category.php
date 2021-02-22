<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=article::class, mappedBy="category")
     */
    private $articles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Collection|article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticles(article $articles): self
    {
        if (!$this->articles->contains($articles)) {
            $this->articles[] = $articles;
            $articles->setCategory($this);
        }

        return $this;
    }

    public function removeArticles(article $articles): self
    {
        if ($this->articles->removeElement($articles)) {
            // set the owning side to null (unless already changed)
            if ($articles->getCategory() === $this) {
                $articles->setCategory(null);
            }
        }

        return $this;
    }


}
