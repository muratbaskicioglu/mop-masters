<?php

namespace App\Service;

use App\DataTransferObject\BookingCreateRequest;
use App\DataTransferObject\BookingUpdateRequest;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BookingService
{
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

    /**
     * BookingService constructor.
     *
     * @param CleanerService $cleanerService
     * @param BookingAssignmentService $bookingAssignmentService
     * @param BookingRepository $bookingRepository
     */
    public function __construct(
        CleanerService $cleanerService,
        BookingAssignmentService $bookingAssignmentService,
        BookingRepository $bookingRepository
    ) {
        $this->cleanerService = $cleanerService;
        $this->bookingAssignmentService = $bookingAssignmentService;
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Calculates end time from start time adding by duration hours.
     *
     * @param $startTime
     * @param $durationByHours
     * @return DateTime
     */
    public function calculateEndTime($startTime, $durationByHours): DateTime
    {
        $timeFormat = $_ENV['TIME_FORMAT'] ?? Booking::TIME_FORMAT;

        return DateTime::createFromFormat(
            $timeFormat,
            $startTime
        )
            ->add(new DateInterval("PT{$durationByHours}H"));
    }

    /**
     * Check if given dates are allowed for booking.
     * This method blocks bookings directly with configurations.
     * We can think of the idea below instead of this implementation.
     * If we want to specify different working hours or holidays for each cleaner,
     * we should implement this logic in CleanerService as two methods.
     * One of those, checks the given time is in cleaners' working times and
     * the second one checks do cleaners have any permit or holiday on those dates.
     *
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return bool
     */
    public function isBookingAllowedAt(DateTimeInterface $startDate, DateTimeInterface $endDate): bool
    {
        $startTimeString = $_ENV['BOOKING_START_TIME_STRING'] ?? Booking::START_TIME_STRING;
        $endTimeString = $_ENV['BOOKING_END_TIME_STRING'] ?? Booking::END_TIME_STRING;
        $holidayOfWeekInNumber = $_ENV['HOLIDAY_OF_WEEK_IN_NUMBER'] ?? Booking::HOLIDAY_OF_WEEK_IN_NUMBER;
        $dateFormat = $_ENV['BOOKING_DATE_FORMAT'] ?? Booking::DATE_FORMAT;
        $timeFormat = $_ENV['BOOKING_TIME_FORMAT'] ?? Booking::TIME_FORMAT;

        // Convert strings and selected dates to date time
        $startTime = DateTime::createFromFormat($timeFormat, $startTimeString);
        $endTime = DateTime::createFromFormat($timeFormat, $endTimeString);
        $selectedStartTime = DateTime::createFromFormat($timeFormat, $startDate->format($timeFormat));
        $selectedEndTime = DateTime::createFromFormat($timeFormat, $endDate->format($timeFormat));

        return
            $startDate > (new DateTime()) && // Should be post-dated
            $selectedStartTime >= $startTime && $selectedStartTime <= $endTime &&
            $selectedEndTime >= $startTime && $selectedEndTime <= $endTime &&
            $startDate->format('N') !== $holidayOfWeekInNumber &&
            $endDate->format('N') !== $holidayOfWeekInNumber
        ;
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
        $timeFormat = $_ENV['BOOKING_TIME_FORMAT'] ?? Booking::TIME_FORMAT;
        $endTime = $this->calculateEndTime($startTime, $durationByHours);

        $startDate = new DateTime(
            $date.
            ' '.
            DateTime::createFromFormat($timeFormat, $startTime)
                ->format($timeFormat)
        );
        $endDate = new DateTime(
            $date.
            ' '.
            DateTime::createFromFormat($timeFormat, $endTime->format($timeFormat))
                ->format($timeFormat)
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
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return bool
     */
    public function checkCleanersAvailability(
        string $bookingId,
        array $cleanerIds,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): bool
    {
        $assignedCleaners = $this->bookingRepository->getAssignedCleaners(
            $bookingId,
            $cleanerIds,
            $startDate,
            $endDate
        );

        return !count($assignedCleaners);
    }

    public function makePreflightChecks(
        string $bookingId,
        array $cleaners,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): void
    {
        $isBookingAllowedAt = $this->isBookingAllowedAt(
            $startDate,
            $endDate
        );

        if (!$isBookingAllowedAt) {
            // TODO: Might be created a class to manage all exception constants
            throw new HttpException(
                Response::HTTP_NOT_ACCEPTABLE,
                // TODO: Might be implemented an internationalization for plain text languages
                'Booking not allowed on these date times.'
            );
        }

        $isCleanersAvailable = $this->checkCleanersAvailability(
            $bookingId,
            $cleaners,
            $startDate,
            $endDate
        );

        if (!$isCleanersAvailable) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                'Cleaners not available at that time.'
            );
        }
    }

    /**
     * Creates new booking.
     *
     * @param BookingCreateRequest $bookingCreateRequest
     * @return Booking
     */
    public function create(BookingCreateRequest $bookingCreateRequest)
    {
        $date = $bookingCreateRequest->getDate();
        $startTime = $bookingCreateRequest->getStartTime();
        $durationByHours = $bookingCreateRequest->getDurationByHours();
        $bookingDates = $this->calculateBookingStartAndEndDate($date, $startTime, $durationByHours);
        $startDate = $bookingDates->getStartDate();
        $endDate = $bookingDates->getEndDate();

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

        $this->makePreflightChecks('', $cleanerIds, $startDate, $endDate);

        $cleaners = [];

        foreach ($cleanerIds as $cleanerId) {
            $cleaners[] = $this->cleanerService->getCleaner($cleanerId);
        }

        return $this->bookingRepository->create(
            $startDate,
            $endDate,
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

        $date = $bookingUpdateRequest->getDate();
        $startTime = $bookingUpdateRequest->getStartTime();
        $durationByHours = $bookingUpdateRequest->getDurationByHours();
        $bookingDates = $this->calculateBookingStartAndEndDate($date, $startTime, $durationByHours);
        $startDate = $bookingDates->getStartDate();
        $endDate = $bookingDates->getEndDate();

        $bookingAssignments = $booking->getBookingAssignments();
        $cleaners = [];

        foreach ($bookingAssignments as $bookingAssignment) {
            $cleaners[] = $bookingAssignment->getCleaner();
        }

        $this->makePreflightChecks($bookingId, $cleaners, $startDate, $endDate);

        return $this->bookingRepository->update(
            $booking,
            $startDate,
            $endDate
        );
    }
}