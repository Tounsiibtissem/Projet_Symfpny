<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Marque $id_marque = null;

    /**
     * @var Collection<int, LignedeCommande>
     */
    #[ORM\OneToMany(targetEntity: LignedeCommande::class, mappedBy: 'produit')]
    private Collection $lignedeCommandes;

    public function __construct()
    {
        $this->lignedeCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getIdMarque(): ?Marque
    {
        return $this->id_marque;
    }

    public function setIdMarque(?Marque $id_marque): static
    {
        $this->id_marque = $id_marque;

        return $this;
    }

    /**
     * @return Collection<int, LignedeCommande>
     */
    public function getLignedeCommandes(): Collection
    {
        return $this->lignedeCommandes;
    }

    public function addLignedeCommande(LignedeCommande $lignedeCommande): static
    {
        if (!$this->lignedeCommandes->contains($lignedeCommande)) {
            $this->lignedeCommandes->add($lignedeCommande);
            $lignedeCommande->setProduit($this);
        }

        return $this;
    }

    public function removeLignedeCommande(LignedeCommande $lignedeCommande): static
    {
        if ($this->lignedeCommandes->removeElement($lignedeCommande)) {
            // set the owning side to null (unless already changed)
            if ($lignedeCommande->getProduit() === $this) {
                $lignedeCommande->setProduit(null);
            }
        }

        return $this;
    }
}
