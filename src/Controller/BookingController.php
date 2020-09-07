<?php

namespace App\Controller;

use App\DataTransferObject\BookingCreateRequest;
use App\DataTransferObject\BookingUpdateRequest;
use App\Repository\BookingRepository;
use App\Service\BookingService;
use App\Service\CleanerService;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Booking;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/bookings", name="bookings_")
 */
class BookingController extends BaseController
{
    /**
     * @Route(name="create", methods={"POST"})
     * @SWG\Response(
     *     response=Response::HTTP_CREATED,
     *     description="Returns the newly created booking.",
     *     @Model(type=Booking::class)
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="JSON string specifying booking details.",
     *     required=true,
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="cleanerId", type="string"),
     *         @SWG\Property(property="date", type="string"),
     *         @SWG\Property(property="startTime", type="string"),
     *         @SWG\Property(property="durationByHours", type="integer")
     *     )
     * )
     * @SWG\Tag(name="bookings")
     *
     * @param BookingCreateRequest $bookingCreateRequest
     * @param BookingService $bookingService
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws HttpException
     */
    public function create(
        BookingCreateRequest $bookingCreateRequest,
        BookingService $bookingService,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $createdBooking = $bookingService->create($bookingCreateRequest);

        return new JsonResponse(
            [
                'id' => $createdBooking->getId(),
                'cleaner' => $createdBooking->getCleaner(),
                'date' => $createdBooking->getDate(),
                'startTime' => $createdBooking->getStartTime(),
                'endTime' => $createdBooking->getEndTime(),
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route(name="list", methods={"GET"})
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns the booking list.",
     *     @SWG\Schema(
     *        type="array",
     *        @SWG\Items(ref=@Model(type=Booking::class))
     *     )
     * )
     * @SWG\Tag(name="bookings")
     *
     * @param BookingService $bookingService
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function list(
        BookingService $bookingService,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $bookings = $bookingService->list();

        return JsonResponse::fromJsonString(
            $serializer->serialize(
                $bookings,
                'json',
                ['groups' => 'booking_list']
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/{bookingId}", name="update", methods={"PATCH"})
     * @SWG\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns updated booking.",
     *     @Model(type=Booking::class)
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="JSON string specifying a date, start time and end time.",
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
     * @param $bookingId
     * @param BookingUpdateRequest $bookingUpdateRequest
     * @param BookingService $bookingService
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws HttpException
     */
    public function update(
        $bookingId,
        BookingUpdateRequest $bookingUpdateRequest,
        BookingService $bookingService,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $updatedBooking = $bookingService->update($bookingId, $bookingUpdateRequest);

        return new JsonResponse(
            [
                'id' => $updatedBooking->getId(),
                'cleanerId' => $updatedBooking->getCleaner()->getId(),
                'date' => $updatedBooking->getDate(),
                'startTime' => $updatedBooking->getStartTime(),
                'endTime' => $updatedBooking->getEndTime(),
            ],
            Response::HTTP_CREATED
        );
    }
}