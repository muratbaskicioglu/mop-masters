<?php

namespace App\Service;

use App\Entity\Cleaner;
use App\Repository\CleanerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CleanerService
{
    /**
     * @var CleanerRepository $cleanerRepository
     */
    private $cleanerRepository;

    /**
     * @var BookingAssignmentService $bookingAssignmentService
     */
    private $bookingAssignmentService;

    public function __construct(
        CleanerRepository $cleanerRepository,
        BookingAssignmentService $bookingAssignmentService
    )
    {
        $this->cleanerRepository = $cleanerRepository;
        $this->bookingAssignmentService= $bookingAssignmentService;
    }

    /**
     * Get given cleaner.
     *
     * @param string|int $cleanerId
     * @return Cleaner|null
     */
    public function getCleaner($cleanerId): ?Cleaner
    {
        return $this->cleanerRepository->find($cleanerId);
    }

    /**
     * Get all cleaners.
     *
     * @returns array
     */
    public function getCleanerList(): array
    {
        return $this->cleanerRepository->findAll();
    }

    /**
     * Check if cleaners are at the same company.
     * Also checks cleaners existing at the same time
     * for query optimization purpose.
     *
     * @param array $cleanerIds
     * @return bool
     */
    public function isCleanersAtSameCompany(array $cleanerIds): bool
    {
        $counts = $this->cleanerRepository->getCompanyCountsOfCleaners($cleanerIds)[0];

        if ((int) $counts['normal_count'] !== count($cleanerIds)) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND,
                'Cleaners doesn\'t exist.'
            );
        }

        return ((int) $counts['distinct_count']) === 1;
    }

    /**
     * Get unavailable(already booked) date times of a cleaner.
     *
     * @param string $cleanerId
     * @return array|int|mixed|string|null
     */
    public function getCleanerUnavailableDateTimes(string $cleanerId): ?array
    {
        $cleaner = $this->getCleaner($cleanerId);

        return $this->bookingAssignmentService->getBookingsOfCleanerAssignments($cleaner);
    }
}