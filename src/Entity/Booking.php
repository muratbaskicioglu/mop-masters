<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    const DURATIONS = [2, 4];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"cleaner_list", "booking_create", "booking_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"cleaner_list", "booking_create", "booking_list", "unavailable_times"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"cleaner_list", "booking_create", "booking_list", "unavailable_times"})
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity=BookingAssignment::class, mappedBy="booking", orphanRemoval=true)
     * @Groups({"booking_list"})
     */
    private $bookingAssignments;

    public function __construct()
    {
        $this->bookingAssignments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate= $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate= $endDate;

        return $this;
    }

    /**
     * @return Collection|BookingAssignment[]
     */
    public function getBookingAssignments(): Collection
    {
        return $this->bookingAssignments;
    }

    public function addBookingAssignment(BookingAssignment $bookingAssignment): self
    {
        if (!$this->bookingAssignments->contains($bookingAssignment)) {
            $this->bookingAssignments[] = $bookingAssignment;
            $bookingAssignment->setBookingId($this);
        }

        return $this;
    }

    public function removeBookingAssignment(BookingAssignment $bookingAssignment): self
    {
        if ($this->bookingAssignments->contains($bookingAssignment)) {
            $this->bookingAssignments->removeElement($bookingAssignment);
            // set the owning side to null (unless already changed)
            if ($bookingAssignment->getBookingId() === $this) {
                $bookingAssignment->setBookingId(null);
            }
        }

        return $this;
    }
}
