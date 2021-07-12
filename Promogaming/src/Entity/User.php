<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("username", message="Ce nom d'utilisateur est dèjà utilisé, veuillez en choisir un autre.")
 * @UniqueEntity("email", message="Cet email est dèjà utilisé, veuillez en choisir un autre.")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_user_login"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"api_user_login"})
     * @Assert\NotBlank
     * @Assert\Regex(pattern="#[\w-]#")
     * @Assert\Length(min=3, max=20,
     * minMessage = "Votre nom d'utilisateur doit faire 3 caractères au minimum",
     * maxMessage = "Votre nom d'utilisateur doit faire 20 caractères au maximum")
     * 
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups({"api_user_login"})
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"api_user_login"})
     * @Assert\NotBlank
     * @Assert\Length(min=3, minMessage = "Votre mot de passe doit faire 3 caractères au minimum")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups({"api_user_login"})
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "L'email '{{ value }}' n'est pas valide."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"api_user_login"})
     *
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_user_login"})
     */
    private $avatar;

    /**
     * 
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="user", fetch="EAGER")
     * @Groups({"api_user_login"})
     */
    private $tags;


    public function __toString()
    {
        return $this->tags;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->tag = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     * @Groups({"api_user_login"})
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addUser($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeUser($this);
        }

        return $this;
    }

}
