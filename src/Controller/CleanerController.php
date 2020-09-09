<?php

namespace App\Controller;

use App\Entity\Cleaner;
use App\Entity\Booking;
use App\Service\CleanerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/cleaners", name="cleaners_")
 */
class CleanerController extends BaseController
{
    /**
     * Get all cleaners with details
     *
     * @Route(name="list", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the detail of given cleaner.",
     *     @SWG\Schema(
     *        type="array",
     *        @SWG\Items(ref=@Model(type=Cleaner::class, groups={"cleaner_list"}))
     *     )
     * )
     * @SWG\Tag(name="cleaners")
     *
     * @param CleanerService $cleanerService
     * @return JsonResponse
     */
    public function list(CleanerService $cleanerService): JsonResponse
    {
        $cleaners = $cleanerService->getCleanerList();

        return $this->toJSON($cleaners, 'cleaner_list');
    }

    /**
     * Get unavailable(already booked) date times of specified cleaner
     *
     * @Route("/{cleanerId}/unavailable-times", name="unavailable_times", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the unavailable times of a cleaner.",
     *     @SWG\Schema(
     *        type="array",
     *        @SWG\Items(ref=@Model(type=Booking::class, groups={"unavailable_times"}))
     *     )
     * )
     * @SWG\Tag(name="cleaners")
     *
     * @param string $cleanerId
     * @param CleanerService $cleanerService
     * @return JsonResponse
     */
    public function getCleanerUnavailableTimes(string $cleanerId, CleanerService $cleanerService)
    {
        $unavailableTimes = $cleanerService->getCleanerUnavailableDateTimes($cleanerId);

        return $this->toJSON($unavailableTimes, 'unavailable_times');
    }
}