<?php

namespace App\Entity;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=App\Repository\TagRepository::class)
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all","api_user_login", "api_tags_all", "api_games_id", "api_games_bytag"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api_games_all","api_user_login", "api_tags_all", "api_games_id", "api_games_id", "api_games_bytag"})
     * 
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="tags")
     * 
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Game::class, mappedBy="tags")
     * 
     */
    private $games;

    /**
     * @ORM\Column(type="integer")
     */
    private $igdb_id;


    public function __construct()
    {
        $this->TagGame = new ArrayCollection();
        $this->TagUser = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->games = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): ?int
    {
        return $this->id = $id;
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

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->addTag($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            $game->removeTag($this);
        }

        return $this;
    }

    public function getIgdbId(): ?int
    {
        return $this->igdb_id;
    }

    public function setIgdbId(int $igdb_id): self
    {
        $this->igdb_id = $igdb_id;

        return $this;
    }

}
