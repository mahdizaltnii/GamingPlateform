<?php

namespace App\Entity;
use App\Entity\User; 
use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostsRepository::class)
 */
class Posts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     * 
     */
    private $id;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="contenu canoot be null")
     * @Assert\Length(min=5 , minMessage = " votre title doit contenir au minimum 5 caratères")
     */
    private $title;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="contenu canoot be null")
     * @Assert\Length(min=5 , minMessage = " votre title doit contenir au minimum 5 caratères")
     */


    private $content;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=255)
     */
    private $picture;

    /**
     * @Groups("post:read")
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="poste", orphanRemoval=true)
     */
    private $comments;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="object canoot be null")
     * @Assert\Length(min=5 , minMessage = " votre title doit contenir au minimum 5 caratères")
     */
    private $Objet;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @Groups("post:read")
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="post",orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="post",orphanRemoval=true)
     */
    private $ratings;

   
    

   

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->likes = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPoste($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPoste() === $this) {
                $comment->setPoste(null);
            }
        }

        return $this;
    }
    
    public function __toString()
{
    return $this->title;
}

    public function getObjet(): ?string
    {
        return $this->Objet;
    }

    public function setObjet(string $Objet): self
    {
        $this->Objet = $Objet;

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

    /**
     * @return Collection<int, PostLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(PostLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(PostLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    /** 
     * @param User $user
     * @return boolean
     */
     public function isLikedByUser(User $user) :bool{

        foreach($this->likes as $like)
        {
            if($like->getUser()===$user)
            return true;
        }
        return false;
     }

     /**
      * @return Collection<int, Rating>
      */
     public function getRatings(): Collection
     {
         return $this->ratings;
     }

     public function addRating(Rating $rating): self
     {
         if (!$this->ratings->contains($rating)) {
             $this->ratings[] = $rating;
             $rating->setPost($this);
         }

         return $this;
     }

     public function removeRating(Rating $rating): self
     {
         if ($this->ratings->removeElement($rating)) {
             // set the owning side to null (unless already changed)
             if ($rating->getPost() === $this) {
                 $rating->setPost(null);
             }
         }

         return $this;
     }
    

    

    
    
}
