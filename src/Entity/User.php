<?php

namespace App\Entity;
use App\Entity\Posts; 
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="email vide")
     */
    private $email;
    /**
     * @ORM\Column(type="json")
     * 
     */
    private $roles = [];
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(min="8",minMessage="votre mot de passe est trop court")
     * @Assert\NotBlank(message="mot de passe vide")
     * @Assert\EqualTo(propertyPath="confirm_password")
     */
    private $password;
    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="users")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $team;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="username canoot be null")
     * @Assert\Length(min=5 , minMessage = " votre title doit contenir au minimum 5 caratères")
     */
    private $username;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture_profil;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isactive;
    /**
     * @Assert\EqualTo(propertyPath="password")
     * @Assert\NotBlank(message="confirm password")
     */
    public $confirm_password;

    /**
     * @ORM\OneToMany(targetEntity=Posts::class, mappedBy="user")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="user")
     */
    private $usercomments;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="user")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="user")
     */
    private $Userratings;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="sender")
     */
    private $sent;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="recipient")
     */
    private $recieved;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $activation_token;

  

    public function __construct()
    {
        $this->userposts = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->usercomments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->Userratings = new ArrayCollection();
        $this->sent = new ArrayCollection();
        $this->recieved = new ArrayCollection();
    }

    
   
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function getUsername(): ?string
    {
        return $this->username;
    }
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function getTeam(): ?Team
    {
        return $this->team;
    }
    public function setTeam(?Team $team): self
    {
        $this->team = $team;
        return $this;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }
    public function getPictureProfil(): ?string
    {
        return $this->picture_profil;
    }
    public function setPictureProfil(?string $picture_profil): self
    {
        $this->picture_profil = $picture_profil;
        return $this;
    }
    public function getIsactive(): ?bool
    {
        return $this->isactive;
    }
    public function setIsactive(bool $isactive): self
    {
        $this->isactive = $isactive;
        return $this;
    }

    /**
     * @return Collection<int, Posts>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getUsercomments(): Collection
    {
        return $this->usercomments;
    }

    public function addUsercomment(Comments $usercomment): self
    {
        if (!$this->usercomments->contains($usercomment)) {
            $this->usercomments[] = $usercomment;
            $usercomment->setUser($this);
        }

        return $this;
    }

    public function removeUsercomment(Comments $usercomment): self
    {
        if ($this->usercomments->removeElement($usercomment)) {
            // set the owning side to null (unless already changed)
            if ($usercomment->getUser() === $this) {
                $usercomment->setUser(null);
            }
        }

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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(PostLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getUserratings(): Collection
    {
        return $this->Userratings;
    }

    public function addUserrating(Rating $userrating): self
    {
        if (!$this->Userratings->contains($userrating)) {
            $this->Userratings[] = $userrating;
            $userrating->setUser($this);
        }

        return $this;
    }

    public function removeUserrating(Rating $userrating): self
    {
        if ($this->Userratings->removeElement($userrating)) {
            // set the owning side to null (unless already changed)
            if ($userrating->getUser() === $this) {
                $userrating->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Messages $sent): self
    {
        if (!$this->sent->contains($sent)) {
            $this->sent[] = $sent;
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Messages $sent): self
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getRecieved(): Collection
    {
        return $this->recieved;
    }

    public function addRecieved(Messages $recieved): self
    {
        if (!$this->recieved->contains($recieved)) {
            $this->recieved[] = $recieved;
            $recieved->setRecipient($this);
        }

        return $this;
    }

    public function removeRecieved(Messages $recieved): self
    {
        if ($this->recieved->removeElement($recieved)) {
            // set the owning side to null (unless already changed)
            if ($recieved->getRecipient() === $this) {
                $recieved->setRecipient(null);
            }
        }

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    

    

   
}
