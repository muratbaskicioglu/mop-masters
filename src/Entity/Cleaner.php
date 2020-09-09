<?php

namespace App\Entity;

use App\Repository\CleanerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CleanerRepository::class)
 */
class Cleaner
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"cleaner_list", "booking_list", "create_booking"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cleaner_list", "booking_list"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(
     *     targetEntity=Company::class,
     *     inversedBy="cleaners",
     * )
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cleaner_list"})
     */
    private $company;

    /**
     * @ORM\OneToMany(
     *     targetEntity=BookingAssignment::class,
     *     mappedBy="cleanerId",
     *     orphanRemoval=true,
     * )
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

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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
            $bookingAssignment->setCleanerId($this);
        }

        return $this;
    }

    public function removeBookingAssignment(BookingAssignment $bookingAssignment): self
    {
        if ($this->bookingAssignments->contains($bookingAssignment)) {
            $this->bookingAssignments->removeElement($bookingAssignment);
            // set the owning side to null (unless already changed)
            if ($bookingAssignment->getCleanerId() === $this) {
                $bookingAssignment->setCleanerId(null);
            }
        }

        return $this;
    }
}
