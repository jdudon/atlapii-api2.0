<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AgglomerationRepository")
 */
class Agglomeration
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Map", inversedBy="agglomerations")
     */
    private $map;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leader;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Building", mappedBy="agglomerations")
     */
    private $buildings;

    public function __construct()
    {
        $this->map = new ArrayCollection();
        $this->buildings = new ArrayCollection();
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
     * @return Collection|Map[]
     */
    public function getMap(): Collection
    {
        return $this->map;
    }

    public function addMap(Map $map): self
    {
        if (!$this->map->contains($map)) {
            $this->map[] = $map;
        }

        return $this;
    }

    public function removeMap(Map $map): self
    {
        if ($this->map->contains($map)) {
            $this->map->removeElement($map);
        }

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getLeader(): ?string
    {
        return $this->leader;
    }

    public function setLeader(?string $leader): self
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * @return Collection|Building[]
     */
    public function getBuildings(): Collection
    {
        return $this->buildings;
    }

    public function addBuilding(Building $building): self
    {
        if (!$this->buildings->contains($building)) {
            $this->buildings[] = $building;
            $building->addAgglomeration($this);
        }

        return $this;
    }

    public function removeBuilding(Building $building): self
    {
        if ($this->buildings->contains($building)) {
            $this->buildings->removeElement($building);
            $building->removeAgglomeration($this);
        }

        return $this;
    }
}
