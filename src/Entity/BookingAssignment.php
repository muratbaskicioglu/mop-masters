<?php

namespace App\Entity;

use App\Repository\BookingAssignmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BookingAssignmentRepository::class)
 */
class BookingAssignment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"booking_list"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Cleaner",
     *     inversedBy="bookingAssignments",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"booking_list"})
     */
    private $cleaner;

    /**
     * @ORM\ManyToOne(targetEntity=Booking::class, inversedBy="bookingAssignments", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $booking;

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

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }
}
