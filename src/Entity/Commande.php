<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, LignedeCommande>
     */
    #[ORM\OneToMany(targetEntity: LignedeCommande::class, mappedBy: 'id_commande')]
    private Collection $lignedeCommandes;

    public function __construct()
    {
        $this->lignedeCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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
            $lignedeCommande->setIdCommande($this);
        }

        return $this;
    }

    public function removeLignedeCommande(LignedeCommande $lignedeCommande): static
    {
        if ($this->lignedeCommandes->removeElement($lignedeCommande)) {
            // set the owning side to null (unless already changed)
            if ($lignedeCommande->getIdCommande() === $this) {
                $lignedeCommande->setIdCommande(null);
            }
        }

        return $this;
    }
}
