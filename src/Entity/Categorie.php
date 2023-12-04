<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="le nom doit etre non vide")
     * @Assert\Length(
     * min=1,
     * max=10,
     * minMessage = "Le nom d'un Event doit comporter au moins {{ limit }} caractères",
     * maxMessage = "Le nom d'un Event doit comporter au moins {{ limit }} caractères",
     * )
     * @Groups("post:read")
     * 
     * 
     * 
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="la description doit etre non vide")
     * @Groups("post:read")
     *  
     * 
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="Categorie", orphanRemoval=true)
     */
    private $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setCategorie($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCategorie() === $this) {
                $event->setCategorie(null);
            }
        }

        return $this;
    }

    


}