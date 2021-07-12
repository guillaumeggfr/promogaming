<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $game_id_igdb;

    /**

     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="game",cascade={"persist"})
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})

     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity=PlateformGame::class, mappedBy="game",cascade={"persist"})
     * @Groups({"api_games_bytag"})
     */
    private $plateformGames;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="games", fetch="EAGER")
     * @ORM\JoinTable(name="game_tag")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    public $tags;

    /**
     * @ORM\Column(type="integer")
     */
    private $game_id_cheapshark;


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->plateformGames = new ArrayCollection();
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

    public function getGameIdIgdb(): ?int
    {
        return $this->game_id_igdb;
    }

    public function setGameIdIgdb(?int $game_id_igdb): self
    {
        $this->game_id_igdb = $game_id_igdb;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setGame($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getGame() === $this) {
                $image->setGame(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|PlateformGame[]
     */
    public function getPlateformGames(): Collection
    {
        return $this->plateformGames;
    }

    public function addPlateformGame(PlateformGame $plateformGame): self
    {
        if (!$this->plateformGames->contains($plateformGame)) {
            $this->plateformGames[] = $plateformGame;
            $plateformGame->setGame($this);
        }

        return $this;
    }

    public function removePlateformGame(PlateformGame $plateformGame): self
    {
        if ($this->plateformGames->removeElement($plateformGame)) {
            // set the owning side to null (unless already changed)
            if ($plateformGame->getGame() === $this) {
                $plateformGame->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getGameIdCheapshark(): ?int
    {
        return $this->game_id_cheapshark;
    }

    public function setGameIdCheapshark(int $game_id_cheapshark): self
    {
        $this->game_id_cheapshark = $game_id_cheapshark;

        return $this;
    }


}
