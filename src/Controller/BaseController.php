<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class BaseController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * JSON response converter.
     *
     * @param mixed $value
     * @param int $status
     * @param string|null $groups
     * @return JsonResponse
     */
    public function toJSON($value, string $groups = null, $status = Response::HTTP_OK): JsonResponse
    {
        return JsonResponse::fromJsonString(
            $this->serializer->serialize($value, 'json', [ 'groups' => $groups ]),
            $status
        );
    }

    /**
     * JSON response converter for created data responses.
     *
     * @param mixed $data
     * @param string|null $groups
     * @return JsonResponse
     */
    public function created($data, string $groups = null): JsonResponse
    {
        return $this->toJSON($data, $groups, Response::HTTP_CREATED);
    }
}