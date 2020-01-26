<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuildingRepository")
 */
class Building
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $function;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leader;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Agglomeration", inversedBy="buildings", cascade={"persist"})
     */
    private $agglomerations;

    public function __construct()
    {
        $this->agglomerations = new ArrayCollection();
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

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(?string $function): self
    {
        $this->function = $function;

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
     * @return Collection|Agglomeration[]
     */
    public function getAgglomerations(): Collection
    {
        return $this->agglomerations;
    }

    public function addAgglomeration(Agglomeration $agglomeration): self
    {
        if (!$this->agglomerations->contains($agglomeration)) {
            $this->agglomerations[] = $agglomeration;
        }

        return $this;
    }

    public function removeAgglomeration(Agglomeration $agglomeration): self
    {
        if ($this->agglomerations->contains($agglomeration)) {
            $this->agglomerations->removeElement($agglomeration);
        }

        return $this;
    }
}
