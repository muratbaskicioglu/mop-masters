<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cleaner::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cleaner;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $startTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $durationByHours;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCleaner(): ?Cleaner
    {
        return $this->cleaner;
    }

    public function setCleaner(?Cleaner $cleaner): self
    {
        $this->cleaner = $cleaner;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getDurationByHours(): ?int
    {
        return $this->durationByHours;
    }

    public function setDurationByHours(int $durationByHours): self
    {
        $this->durationByHours = $durationByHours;

        return $this;
    }
}
