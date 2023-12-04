<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Serializer\Annotation\Groups;












/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *  @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     * min=1,
     * max=100,
     * minMessage = "Le nom d'un Event doit comporter au moins {{ limit }} caractères",
     * maxMessage = "Le nom d'un Event doit comporter au moins {{ limit }} caractères",
     * )
     * @Assert\NotBlank(message="le nom doit etre non vide")
     * @Groups("post:read")
     */
    private $Name;

    /**
     * @ORM\Column(type="date")
     *  @Groups("post:read")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     * min=1,
     * max=100,
     * minMessage = "Le nom d'un Event doit comporter au moins {{ limit }} caractères",
     * maxMessage = "Le nom d'un Event doit comporter au moins {{ limit }} caractères",
     * )
     *   @Groups("post:read")
     *  @Assert\NotBlank(message="le lieu doit etre non vide")
     */
    private $lieu;

    /**
     * @ORM\Column(type="text")
     *  @Assert\NotBlank(message="le description doit etre non vide")
     * *  @Groups("post:read")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups("post:read")
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     *  @Groups("post:read")
     */
    private $Categorie;

  
    



     

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    



    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture( $picture)
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->Categorie;
    }

    public function setCategorie(?Categorie $Categorie): self
    {
        $this->Categorie = $Categorie;

        return $this;
    }


    






}