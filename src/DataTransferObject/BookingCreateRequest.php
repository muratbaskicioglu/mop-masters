<?php

namespace App\DataTransferObject;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Booking;

class BookingCreateRequest implements RequestDTOInterface
{
    /**
     * @Assert\NotBlank()
     * @SWG\Property(type="array", description="The array of cleaners.")
     * @var array
     */
    private $cleanerIds;

    /**
     * @Assert\NotBlank()
     * @Assert\Date
     * @SWG\Property(type="string")
     * @var string A "Y-m-d" formatted value
     */
    private $date;

    /**
     * @Assert\NotBlank()
     * @Assert\Time
     * @SWG\Property(type="string")
     * @var string A "H:i:s" formatted value
     */
    private $startTime;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices=Booking::DURATIONS, message="Choose a valid service duration by hours.")
     * @SWG\Property(type="integer")
     * @var integer
     */
    private $durationByHours;

    public function __construct(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $this->cleanerIds = $data['cleanerIds'] ?? '';
        $this->date = $data['date'] ?? '';
        $this->startTime = $data['startTime'] ?? '';
        $this->durationByHours = $data['durationByHours'] ?? '';
    }

    public function getCleanerIds(): array
    {
        return $this->cleanerIds;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getDurationByHours(): int
    {
        return $this->durationByHours;
    }
}