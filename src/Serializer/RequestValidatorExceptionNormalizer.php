<?php

namespace App\Serializer;

use App\Exception\RequestValidatorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RequestValidatorExceptionNormalizer implements NormalizerInterface
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param RequestValidatorException $exception
     * @param null $format
     * @param array $context
     *
     * @return array|bool|float|int|string|void
     */
    public function normalize($exception, $format = null, array $context = [])
    {
        return [];
    }

    /**
     * @param mixed $data
     * @param null $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null): ?bool
    {
        return false;
    }
}
