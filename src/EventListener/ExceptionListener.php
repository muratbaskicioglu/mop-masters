<?php

namespace App\EventListener;

use App\Factory\NormalizerFactory;
use App\Http\ApiResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Throwable;

class ExceptionListener
{
    /**
     * @var NormalizerFactory
     */
    private $normalizerFactory;
    private $logger;

    /**
     * ExceptionListener constructor.
     *
     * @param NormalizerFactory $normalizerFactory
     */
    public function __construct(NormalizerFactory $normalizerFactory, LoggerInterface $logger)
    {
        $this->normalizerFactory = $normalizerFactory;
        $this->logger = $logger;
    }

    /**
     * @param ExceptionEvent $event
     * @throws ExceptionInterface
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            $response = $this->createApiResponse($exception);
            $event->setResponse($response);
        }
    }

    /**
     * Creates the ApiResponse from any Exception
     *
     * @param Throwable $exception
     *
     * @return ApiResponse
     * @throws ExceptionInterface
     */
    private function createApiResponse(Throwable $exception)
    {
        $normalizer = $this->normalizerFactory->getNormalizer($exception);
        $statusCode = $exception instanceof HttpExceptionInterface ?
            $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        try {
            $errors = $normalizer ? $normalizer->normalize($exception) : [];
        } catch (\Exception $e) {
            $errors = [];
        }

        return new ApiResponse($exception->getMessage(), null, $errors, $statusCode);
    }
}