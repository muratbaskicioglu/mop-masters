<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionNormalizer implements NormalizerInterface
{
    /**
     * @param HttpException $exception
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
        return $data instanceof HttpException;
    }
}