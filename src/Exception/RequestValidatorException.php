<?php

namespace App\Exception;

use App\DataTransferObject\Violation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidatorException extends BadRequestHttpException
{
    /**
     * @var object
     */
    protected $error;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * HttpFormException constructor.
     *
     * @param ConstraintViolationListInterface $validatorErrors
     * @param SerializerInterface $serializer
     */
    public function __construct(ConstraintViolationListInterface $validatorErrors, SerializerInterface $serializer)
    {
        parent::__construct();

        $this->serializer = $serializer;
        $this->error = $this->parseValidatorErrors($validatorErrors);
        $this->message = $this->error->title;
    }

    /**
     * @param ConstraintViolationListInterface $validatorErrors
     *
     * @return mixed
     */
    public function parseValidatorErrors(ConstraintViolationListInterface $validatorErrors) {
        $serializedErrors = $this->serializer->serialize($validatorErrors, 'json');

        return (new JsonDecode())->decode($serializedErrors, JsonEncoder::FORMAT);
    }

    /**
     * @param object $errorViolation
     *
     * @return Violation
     */
    public function convertErrorViolation(object $errorViolation): Violation
    {
        $violation = new Violation();
        $violation->property = $errorViolation->propertyPath;
        $violation->message = $errorViolation->title;

        return $violation;
    }

    /**
     * @return iterable|array|null
     */
    public function getViolations(): ?iterable
    {
        $violations = [];

        foreach ($this->error->violations as $errorViolation) {
            $errors[] = $this->convertErrorViolation($errorViolation);
        }

        return $violations;
    }
}