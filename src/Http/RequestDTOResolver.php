<?php

namespace App\Http;

use App\DataTransferObject\RequestDTOInterface;
use App\Exception\RequestValidatorException;
use Generator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ValidatorInterface $validator
     */
    private $validator;

    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    private $logger;

    /**
     * RequestDTOResolver constructor.
     *
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * Check support.
     *
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument): ?bool
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

    /**
     * Validate and resolve the value of DTO.
     *
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return Generator|iterable
     */
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
