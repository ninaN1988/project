<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NodesRepository")
 */
class Nodes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
	
	/**
     * @ORM\ManyToMany(targetEntity="App\Entity\Schedule", inversedBy="nodes")
     */
	private $schedules;
	
	/**
     * @ORM\ManyToMany(targetEntity="App\Entity\City", inversedBy="nodes")
     */
	private $cities;
	
	/**
     * @ORM\Column(type="boolean")
     */
	private $nodesOk;
	
	/**
     * @ORM\ManyToOne(targetEntity="App\Entity\Schedule", inversedBy="nodes")
     */
	private $schedule;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
        $this->cities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNodesOk(): ?bool
    {
        return $this->nodesOk;
    }

    public function setNodesOk(bool $nodesOk): self
    {
        $this->nodesOk = $nodesOk;

        return $this;
    }

    /**
     * @return Collection|Schedule[]
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules[] = $schedule;
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
        }

        return $this;
    }

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
        }

        return $this;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }
}
