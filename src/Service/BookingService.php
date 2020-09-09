<?php

namespace App\Service;

use App\DataTransferObject\BookingCreateRequest;
use App\DataTransferObject\BookingUpdateRequest;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use DateInterval;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BookingService
{
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';

    private $logger;

    /**
     * @var CleanerService $cleanerService
     */
    private $cleanerService;

    /**
     * @var BookingAssignmentService $bookingAssignmentService
     */
    private $bookingAssignmentService;

    /**
     * @var BookingRepository $bookingRepository
     */
    private $bookingRepository;

    public function __construct(
        LoggerInterface $logger,
        CleanerService $cleanerService,
        BookingAssignmentService $bookingAssignmentService,
        BookingRepository $bookingRepository
    ) {
        $this->logger = $logger;
        $this->cleanerService = $cleanerService;
        $this->bookingAssignmentService = $bookingAssignmentService;
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Calculates booking start and end date as formatted.
     *
     * @param string $date
     * @param string $startTime
     * @param int $durationByHours
     * @return Booking
     */
    public function calculateBookingStartAndEndDate(string $date, string $startTime, int $durationByHours): ?Booking
    {
        $endTime = DateTime::createFromFormat(
            self::TIME_FORMAT,
            $startTime
        )
            ->add(new DateInterval("PT{$durationByHours}H"));
        $startDate = new DateTime(
            DateTime::createFromFormat(self::DATE_FORMAT, $date)
                ->format(self::DATE_FORMAT).
            ' '.
            DateTime::createFromFormat(self::TIME_FORMAT, $startTime)
                ->format(self::TIME_FORMAT)
        );
        $endDate = new DateTime(
            DateTime::createFromFormat(self::DATE_FORMAT, $date)
                ->format(self::DATE_FORMAT).
            ' '.
            DateTime::createFromFormat(self::TIME_FORMAT, $endTime->format(self::TIME_FORMAT))
                ->format(self::TIME_FORMAT)
        );

        $bookingDates = new Booking();
        $bookingDates->setStartDate($startDate);
        $bookingDates->setEndDate($endDate);

        return $bookingDates;
    }

    /**
     * Check if all cleaners are available.
     *
     * @param string $bookingId
     * @param array $cleanerIds
     * @param string $date
     * @param string $startTime
     * @param int $durationByHours
     * @return bool
     */
    public function checkCleanersAvailability(
        string $bookingId,
        array $cleanerIds,
        string $date,
        string $startTime,
        int $durationByHours
    ): bool
    {
        $booking = $this->calculateBookingStartAndEndDate($date, $startTime, $durationByHours);

        $assignedCleaners = $this->bookingRepository->getAssignedCleaners(
            $bookingId,
            $cleanerIds,
            $booking->getStartDate(),
            $booking->getEndDate()
        );

        return !count($assignedCleaners);
    }

    /**
     * Creates new booking.
     *
     * @param BookingCreateRequest $bookingCreateRequest
     * @return Booking
     */
    public function create(BookingCreateRequest $bookingCreateRequest)
    {
        $cleanerIds = $bookingCreateRequest->getCleanerIds();

        $isCleanersAtSameCompany = $this->cleanerService->isCleanersAtSameCompany(
            $cleanerIds
        );

        if (!$isCleanersAtSameCompany) {
            throw new HttpException(
                Response::HTTP_NOT_ACCEPTABLE,
                'Cleaners must be from the same company.'
            );
        }

        $isCleanersAvailable = $this->checkCleanersAvailability(
            '',
            $bookingCreateRequest->getCleanerIds(),
            $bookingCreateRequest->getDate(),
            $bookingCreateRequest->getStartTime(),
            $bookingCreateRequest->getDurationByHours(),
        );

        $this->logger->info($isCleanersAvailable);

        if (!$isCleanersAvailable) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Cleaners not available at that time.'
            );
        }

        $booking = $this->calculateBookingStartAndEndDate(
            $bookingCreateRequest->getDate(),
            $bookingCreateRequest->getStartTime(),
            $bookingCreateRequest->getDurationByHours()
        );

        $cleaners = [];

        foreach ($cleanerIds as $cleanerId) {
            $cleaners[] = $this->cleanerService->getCleaner($cleanerId);
        }

        return $this->bookingRepository->create(
            $booking->getStartDate(),
            $booking->getEndDate(),
            $cleaners
        );
    }

    /**
     * Get the booking.
     *
     * @param string $bookingId
     * @return Booking|null
     */
    public function get(string $bookingId): ?Booking
    {
        return $this->bookingRepository->find($bookingId);
    }

    /**
     * Get all bookings.
     *
     * @return array|null
     */
    public function list(): ?array
    {
        return $this->bookingRepository->findAll();
    }

    /**
     * @param string $bookingId
     * @param BookingUpdateRequest $bookingUpdateRequest
     * @return Booking|null
     */
    public function update(string $bookingId, BookingUpdateRequest $bookingUpdateRequest): ?Booking
    {
        $booking = $this->get($bookingId);

        if (!$booking) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND,
                'No booking found with '.$bookingId.'.'
            );
        }

        $bookingAssignments = $booking->getBookingAssignments();
        $cleaners = [];

        foreach ($bookingAssignments as $bookingAssignment) {
            $cleaners[] = $bookingAssignment->getCleaner();
        }

        $isCleanersAvailable = $this->checkCleanersAvailability(
            $bookingId,
            $cleaners,
            $bookingUpdateRequest->getDate(),
            $bookingUpdateRequest->getStartTime(),
            $bookingUpdateRequest->getDurationByHours()
        );

        $this->logger->info($isCleanersAvailable);

        if (!$isCleanersAvailable) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Cleaners not available at that time.'
            );
        }

        $bookingDates = $this->calculateBookingStartAndEndDate(
            $bookingUpdateRequest->getDate(),
            $bookingUpdateRequest->getStartTime(),
            $bookingUpdateRequest->getDurationByHours()
        );

        return $this->bookingRepository->update(
            $booking,
            $bookingDates->getStartDate(),
            $bookingDates->getEndDate()
        );
    }
}