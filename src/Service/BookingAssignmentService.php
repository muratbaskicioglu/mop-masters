<?php

namespace App\Service;

use App\Entity\Cleaner;
use App\Repository\BookingAssignmentRepository;

class BookingAssignmentService
{
    /**
     * @var BookingAssignmentRepository
     */
    private $bookingAssignmentRepository;

    public function __construct(BookingAssignmentRepository $bookingAssignmentRepository)
    {
        $this->bookingAssignmentRepository = $bookingAssignmentRepository;
    }

    /**
     * Get bookings of cleaner assignments.
     *
     * @param Cleaner $cleaner
     * @return array|int|mixed|string|null
     */
    public function getBookingsOfCleanerAssignments(Cleaner $cleaner): ?array
    {
        return $this->bookingAssignmentRepository->getBookingsOfCleanerAssignments($cleaner);
    }
}