<?php

namespace App\Service;

use App\DataTransferObject\BookingCreateRequest;
use App\DataTransferObject\BookingUpdateRequest;
use App\Entity\Booking;
use App\Entity\Cleaner;
use App\Repository\BookingRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\ORMException;
use App\Service\CleanerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class BookingService
{
    private $logger;

    private $cleanerService;

    private $bookingRepository;

    public function __construct(LoggerInterface $logger, CleanerService $cleanerService, BookingRepository $bookingRepository)
    {
        $this->logger = $logger;
        $this->cleanerService = $cleanerService;
        $this->bookingRepository = $bookingRepository;
    }

    public function get($bookingId): ?Booking
    {
        return $this->bookingRepository->find($bookingId);
    }

    public function create(BookingCreateRequest $bookingCreateRequest): ?Booking
    {
        $cleaner = $this->cleanerService->getCleaner($bookingCreateRequest->getCleanerId());

        $date = DateTime::createFromFormat('Y-m-d', $bookingCreateRequest->getDate());
        $startTime = DateTime::createFromFormat('H:i:s', $bookingCreateRequest->getStartTime());
        $endTime = DateTime::createFromFormat(
            'H:i:s',
            $bookingCreateRequest->getStartTime()
        )
            ->add(new DateInterval("PT{$bookingUpdateRequest->getDurationByHours()}H"));

        $isCleanerAvailable = $this->cleanerService->isCleanerAvailableAt(
            $cleaner,
            $date,
            $startTime,
            $endTime,
        );

        $this->logger->info('Is cleaner available: '.$isCleanerAvailable);

        if (!$isCleanerAvailable) {
            throw new \Exception(
                'This cleaner isn\'t available at that time.',
                Response::HTTP_CONFLICT
            );
        }

        return $this->bookingRepository->create($cleaner, $date, $startTime, $endTime);
    }

    public function list(): ?array
    {
        return $this->bookingRepository->findAll();
    }

    public function update($bookingId, BookingUpdateRequest $bookingUpdateRequest): ?Booking
    {
        $booking = $this->get($bookingId);

        if (!$booking) {
            throw new \Exception(
                'No booking found for id '.$bookingId,
                Response::HTTP_NOT_FOUND
            );
        }

        $date = DateTime::createFromFormat('Y-m-d', $bookingUpdateRequest->getDate());
        $startTime = DateTime::createFromFormat('H:i:s', $bookingUpdateRequest->getStartTime());
        $endTime = DateTime::createFromFormat(
            'H:i:s',
            $bookingUpdateRequest->getStartTime()
        )
            ->add(new DateInterval("PT{$bookingUpdateRequest->getDurationByHours()}H"));

        $isCleanerAvailable = $this->cleanerService->isCleanerAvailableAt(
            $booking->getCleaner(),
            $date,
            $startTime,
            $endTime,
        );

        if (!$isCleanerAvailable) {
            throw new \Exception(
                'This cleaner isn\'t available at that time.',
                Response::HTTP_CONFLICT
            );
        }

        return $this->bookingRepository->update($booking, $date, $startTime, $endTime);
    }
}