<?php

namespace App\Controller;

use App\Repository\CleanerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;


class CleanerController extends AbstractController
{
    /**
     * @Route("/cleaners", name="app_cleaner_list")
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