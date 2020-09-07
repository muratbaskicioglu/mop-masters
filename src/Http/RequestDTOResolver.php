<?php

namespace App\DataTransferObject;

use App\Exception\RequestValidatorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    private $validator;
    private $logger;
    private $serializer;

    public function __construct(ValidatorInterface $validator, LoggerInterface $logger, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        try {
            $reflection = new \ReflectionClass($argument->getType());

            if ($reflection->implementsInterface(RequestDTOInterface::class)) {
                return true;
            }

            return false;
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class = $argument->getType();
        $dto = new $class($request);
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new RequestValidatorException($errors, $this->serializer);
        }

        yield $dto;
    }
}
