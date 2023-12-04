<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LivraisonRepository::class)
 */
class Livraison
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $Payment;

    /**
     * @ORM\OneToOne(targetEntity=Commande::class, inversedBy="livraison", cascade={"persist", "remove"})
     */
    private $commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(?string $Status): self
    {
        $this->Status = $Status;

        return $this;
    }

    public function getPayment(): ?bool
    {
        return $this->Payment;
    }

    public function setPayment(?bool $Payment): self
    {
        $this->Payment = $Payment;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
    public function __toString()
    {
        // to show the name of the Category in the select
        return(string) $this->id;
        // to show the id of the Category in the select
        // return $this->id;
    }
}
