<?php

namespace App\Entity;

use App\Repository\PlateformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlateformRepository::class)
 */
class Plateform
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_games_all", "api_games_id", "plateforms", "api_games_bytag"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"api_games_all", "api_games_id", "plateforms", "api_games_bytag"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=PlateformGame::class, mappedBy="plateform")
     */
    private $plateformGames;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="integer")
     */
    private $apistore_id;

    public function __construct()
    {
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
            $plateformGame->setPlateform($this);
        }

        return $this;
    }

    public function removePlateformGame(PlateformGame $plateformGame): self
    {
        if ($this->plateformGames->removeElement($plateformGame)) {
            // set the owning side to null (unless already changed)
            if ($plateformGame->getPlateform() === $this) {
                $plateformGame->setPlateform(null);
            }
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getApistoreId(): ?int
    {
        return $this->apistore_id;
    }

    public function setApistoreId(int $apistore_id): self
    {
        $this->apistore_id = $apistore_id;

        return $this;
    }

}
