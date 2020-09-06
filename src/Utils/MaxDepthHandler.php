<?php

namespace App\Utils;

class MaxDepthHandler
{
    public function __invoke($innerObject, $outerObject, string $attributeName, string $format = null, array $context = [])
    {
        return $innerObject->id;
    }
}