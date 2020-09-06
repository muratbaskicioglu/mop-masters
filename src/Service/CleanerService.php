<?php

namespace App\Service;

use App\DataTransferObject\BookingCreateRequest;
use App\Repository\BookingRepository;
use App\Repository\CleanerRepository;
use DateInterval;
use DateTime;

class CleanerService
{
    private $cleanerRepository;

    private $bookingRepository;

    public function __construct(CleanerRepository $cleanerRepository, BookingRepository $bookingRepository)
    {
        $this->cleanerRepository = $cleanerRepository;
        $this->bookingRepository = $bookingRepository;
    }

    public function getCleaner($cleanerId)
    {
        return $this->cleanerRepository->find($cleanerId);
    }

    public function isCleanerAvailableAt($cleaner, $date, $startTime, $endTime)
    {
        return !count(
            $this->bookingRepository->findByCleanerIdBetweenDateTime(
                $cleaner,
                $date,
                $startTime,
                $endTime
            )
        );
    }
}