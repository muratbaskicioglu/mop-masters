<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"cleaner_list", "booking_list"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cleaner::class, inversedBy="bookings", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"booking_list"})
     */
    private $cleaner;

    /**
     * @ORM\Column(type="date")
     * @Groups({"cleaner_list", "booking_list"})
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     * @Groups({"cleaner_list", "booking_list"})
     */
    private $startTime;

    /**
     * @ORM\Column(type="time")
     * @Groups({"cleaner_list", "booking_list"})
     */
    private $endTime;

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

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime= $endTime;

        return $this;
    }
}
