<?php

namespace App\Entity;

use App\Repository\LignedeCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LignedeCommandeRepository::class)]
class LignedeCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $quantité = null;

    #[ORM\ManyToOne(inversedBy: 'lignedeCommandes')]
    private ?product $produit = null;

    #[ORM\ManyToOne(inversedBy: 'lignedeCommandes')]
    private ?Commande $id_commande = null;

    #[ORM\ManyToOne(inversedBy: 'lignedeCommandes')]
    private ?Panier $Id_panier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantité(): ?string
    {
        return $this->quantité;
    }

    public function setQuantité(string $quantité): static
    {
        $this->quantité = $quantité;

        return $this;
    }

    public function getProduit(): ?product
    {
        return $this->produit;
    }

    public function setProduit(?product $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getIdCommande(): ?Commande
    {
        return $this->id_commande;
    }

    public function setIdCommande(?Commande $id_commande): static
    {
        $this->id_commande = $id_commande;

        return $this;
    }

    public function getIdPanier(): ?Panier
    {
        return $this->Id_panier;
    }

    public function setIdPanier(?Panier $Id_panier): static
    {
        $this->Id_panier = $Id_panier;

        return $this;
    }
}
