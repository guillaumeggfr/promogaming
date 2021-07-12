<?php

namespace App\Entity;

use App\Repository\PlateformGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlateformGameRepository::class)
 */
class PlateformGame
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $initial_price;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $actual_price;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $reduce;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Plateform::class, inversedBy="plateformGames")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"api_games_all", "api_games_id", "plateforms", "api_games_bytag"})
     */
    private $plateform;
    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="plateformGames", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"api_games_all", "api_games_id"})
     */
    private $game;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api_games_all", "api_games_id", "api_games_bytag"})
     */
    private $dealID;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitialPrice(): ?int
    {
        return $this->initial_price;
    }

    public function setInitialPrice(int $initial_price): self
    {
        $this->initial_price = $initial_price;

        return $this;
    }

    public function getActualPrice(): ?int
    {
        return $this->actual_price;
    }

    public function setActualPrice(int $actual_price): self
    {
        $this->actual_price = $actual_price;

        return $this;
    }

    public function getReduce(): ?int
    {
        return $this->reduce;
    }

    public function setReduce($reduce): self
    {
        $this->reduce = $reduce;

        return $this;
    }

    public function getIsActive(): ?int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPlateform(): ?Plateform
    {
        return $this->plateform;
    }

    public function setPlateform(?Plateform $plateform): self
    {
        $this->plateform = $plateform;

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

    public function getDealID(): ?string
    {
        return $this->dealID;
    }

    public function setDealID(string $dealID): self
    {
        $this->dealID = $dealID;

        return $this;
    }

}
