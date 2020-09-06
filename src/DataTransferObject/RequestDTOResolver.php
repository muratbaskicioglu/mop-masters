<?php

namespace App\DataTransferObject;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
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
            // TODO: Error messages might be made more human-readable
            throw new BadRequestHttpException((string) $errors);
        }

        yield $dto;
    }
}
