<?php

namespace App\Controller;

use App\Repository\CleanerRepository;
use App\Entity\Cleaner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

/**
 * @Route("/cleaners", name="cleaner_")
 */
class CleanerController extends BaseController
{
    /**
     * @Route(name="list", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     *     @SWG\Schema(
     *        type="array",
     *        @SWG\Items(ref=@Model(type=Cleaner::class, groups={"cleaner_list"}))
     *     )
     * )
     * @SWG\Tag(name="cleaners")
     *
     * @param CleanerRepository $cleanerRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function list(CleanerRepository $cleanerRepository, SerializerInterface $serializer): JsonResponse
    {
        $cleaners = $cleanerRepository->findAll();

        return JsonResponse::fromJsonString(
            $serializer->serialize(
                $cleaners,
                'json',
                ['groups' => 'cleaner_list']
            ),
            Response::HTTP_OK
        );
    }
}