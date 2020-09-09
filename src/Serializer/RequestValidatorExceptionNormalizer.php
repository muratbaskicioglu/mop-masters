<?php

namespace App\Serializer;

use App\Exception\RequestValidatorException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RequestValidatorExceptionNormalizer implements NormalizerInterface
{
    /**
     * @param $exception
     * @param null $format
     * @param array $context
     *
     * @return array|bool|float|int|string|void
     */
    public function normalize($exception, $format = null, array $context = [])
    {
        return $exception->convertViolationListToErrors();
    }

    /**
     * @param mixed $data
     * @param null $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null): ?bool
    {
        return $data instanceof RequestValidatorException;
    }
}
