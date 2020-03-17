<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="schedule")
     */
    private $cityStart;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="schedule")
     */
    private $cityEnd;
	
    /**
     * @ORM\Column(type="time")
     */
    private $timeStart;

    /**
     * @ORM\Column(type="time")
     */
    private $timeEnd;
	
	//type should be enum...
	/**
     * @ORM\Column(type="string", length=255) 
     */
	private $status;
	
	/**
     * @ORM\OneToMany(targetEntity="App\Entity\Nodes", mappedBy="schedule")
     */
    private $nodes;
	
	/**
     * @ORM\Column(type="integer") 
     */
	private $distance;
		
	public function __construct()
          {
              $this->status = 'unvisited';
              $this->nodes = new ArrayCollection();
          }
	
	public function setTimeEnd(\DateTimeInterface $timeEnd): self
          {
              $this->timeEnd->setDate(8,11,1988);
              $this->timeEnd->setTime(
                  $timeEnd->format('G'),
                  $timeEnd->format('i')
              );
              return $this;
          }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeStart(): ?\DateTimeInterface
    {
        return $this->timeStart;
    }

    public function setTimeStart(\DateTimeInterface $timeStart): self
    {
        $this->timeStart = $timeStart;

        return $this;
    }

    public function getTimeEnd(): ?\DateTimeInterface
    {
        return $this->timeEnd;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCityStart(): ?City
    {
        return $this->cityStart;
    }

    public function setCityStart(?City $cityStart): self
    {
        $this->cityStart = $cityStart;

        return $this;
    }

    public function getCityEnd(): ?City
    {
        return $this->cityEnd;
    }

    public function setCityEnd(?City $cityEnd): self
    {
        $this->cityEnd = $cityEnd;

        return $this;
    }

    /**
     * @return Collection|Nodes[]
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(Nodes $node): self
    {
        if (!$this->nodes->contains($node)) {
            $this->nodes[] = $node;
            $node->setSchedule($this);
        }

        return $this;
    }

    public function removeNode(Nodes $node): self
    {
        if ($this->nodes->contains($node)) {
            $this->nodes->removeElement($node);
            // set the owning side to null (unless already changed)
            if ($node->getSchedule() === $this) {
                $node->setSchedule(null);
            }
        }

        return $this;
    }

	//json_encode(MyScheduleEntityObject);
	public function jsonSerialize()
          {
      		return array(
			'id' => $this->getId(),
				  'distance' => $this->getDistance(),
                  'cityStart' => $this->cityStart->getName(),
                  'cityEnd'=> $this->cityEnd->getName(),
                  'timeStart' => $this->timeStart->format('H:i'),
                  'timeEnd'=> $this->timeEnd->format('H:i'),
              );
          }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }
}
