<?php

namespace App\Controller;

use App\DataTransferObject\BookingCreateRequest;
use App\DataTransferObject\BookingUpdateRequest;
use App\Service\BookingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Booking;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/bookings", name="bookings_")
 */
class BookingController extends BaseController
{
    /**
     * Creates new bookings
     *
     * @Route(name="create", methods={"POST"})
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the newly created booking.",
     *     @Model(type=Booking::class, groups={"booking_create"})
     * ),
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="JSON string that specifying booking details.",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="cleanerIds",
     *             type="array",
     *             @SWG\Items(type="number")
     *         ),
     *         @SWG\Property(property="date", type="string"),
     *         @SWG\Property(property="startTime", type="string"),
     *         @SWG\Property(property="durationByHours", type="integer")
     *     )
     * ),
     * @SWG\Tag(name="bookings")
     *
     * @param BookingCreateRequest $bookingCreateRequest
     * @param BookingService $bookingService
     * @return JsonResponse
     * @throws HttpException
     */
    public function create(
        BookingCreateRequest $bookingCreateRequest,
        BookingService $bookingService
    ): JsonResponse
    {
        $cleanerIds = $bookingCreateRequest->getCleanerIds();

        if (count(array_unique($cleanerIds)) !== count($cleanerIds)) {
            throw new BadRequestHttpException('You can\'t assign the same cleaner twice.');
        }

        $createdBooking = $bookingService->create(
            $bookingCreateRequest
        );

        return $this->created($createdBooking, 'booking_create');
    }

    /**
     * Get list of bookings
     *
     * @Route(name="list", methods={"GET"})
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns all bookings.",
     *     @SWG\Schema(
     *        type="array",
     *        @SWG\Items(ref=@Model(type=Booking::class, groups={"booking_list"}))
     *     )
     * )
     * @SWG\Tag(name="bookings")
     *
     * @param BookingService $bookingService
     * @return JsonResponse
     */
    public function list(BookingService $bookingService): JsonResponse
    {
        $bookings = $bookingService->list();

        return $this->toJSON($bookings,'booking_list');
    }

    /**
     * Update current booking with new dates
     *
     * @Route("/{bookingId}", name="update", methods={"PATCH"})
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns updated booking.",
     *     @Model(type=Booking::class, groups={"booking_create"})
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="JSON string that specifying a date, start time and duration by hours.",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="date", type="string"),
     *         @SWG\Property(property="startTime", type="string"),
     *         @SWG\Property(property="durationByHours", type="integer")
     *     )
     * )
     * @SWG\Tag(name="bookings")
     *
     * @param string $bookingId
     * @param BookingUpdateRequest $bookingUpdateRequest
     * @param BookingService $bookingService
     * @return JsonResponse
     * @throws HttpException
     */
    public function update(
        string $bookingId,
        BookingUpdateRequest $bookingUpdateRequest,
        BookingService $bookingService
    ): JsonResponse
    {
        $updatedBooking = $bookingService->update($bookingId, $bookingUpdateRequest);

        return $this->toJSON($updatedBooking, 'booking_create');
    }
}