<?php

namespace App\Entity;
use App\Entity\User; 
use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentsRepository::class)
 */
class Comments
{
    /**
     * @Groups("post:read")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="contenu commentaire canoot be null")
     * @Assert\Length(min=5 , minMessage = " votre title doit contenir au minimum 5 caratères")
     */
    private $content;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Posts::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=true)
     */
    private $poste;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="usercomments")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    
      
    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPoste(): ?Posts
    {
        return $this->poste;
    }

    public function setPoste(?Posts $poste): self
    {
        $this->poste = $poste;

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

    
}
