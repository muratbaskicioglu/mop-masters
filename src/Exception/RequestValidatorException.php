<?php

namespace App\Exception;

use App\DataTransferObject\Violation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidatorException extends BadRequestHttpException
{
    /**
     * @var ConstraintViolationListInterface $violationList
     */
    protected $violationList;

    /**
     * RequestValidatorException constructor.
     *
     * @param ConstraintViolationListInterface $violationList
     */
    public function __construct(ConstraintViolationListInterface $violationList)
    {
        parent::__construct();

        $this->violationList = $violationList;

        // TODO: Might be created a class to manage all exception constants
        $this->message = 'Request payload validation error.';
        $this->code = -1;
    }

    /**
     * @return mixed
     */
    public function convertViolationListToErrors(): array
    {
        $errors = [];

        foreach ($this->violationList as $violation) {
            $errors[] = $this->createViolationObject($violation);
        }

        return $errors;
    }

    /**
     * @param object $violation
     * @return Violation
     */
    public function createViolationObject($violation): Violation
    {
        $violationObject = new Violation();
        $violationObject->property = $violation->getPropertyPath();
        $violationObject->message = $violation->getMessage();

        return $violationObject;
    }
}