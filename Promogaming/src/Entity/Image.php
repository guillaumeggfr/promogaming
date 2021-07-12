<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="images")
     */
    private $game;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_igdb;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getIdIgdb(): ?int
    {
        return $this->id_igdb;
    }

    public function setIdIgdb(int $id_igdb): self
    {
        $this->id_igdb = $id_igdb;

        return $this;
    }
}
