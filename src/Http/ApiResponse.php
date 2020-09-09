<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * ApiResponse constructor.
     *
     * @param string $message
     * @param mixed $data
     * @param array $errors
     * @param int $code
     * @param int $status
     * @param array $headers
     * @param bool $json
     */
    public function __construct(
        string $message,
        $data = null,
        array $errors = [],
        int $code = 0,
        int $status = 200,
        array $headers = [],
        bool $json = false
    )
    {
        parent::__construct($this->format($message, $data, $errors, $code), $status, $headers, $json);
    }

    /**
     * Format the API response.
     *
     * @param string $message
     * @param mixed $data
     * @param array $errors
     * @param int $code
     * @return array
     */
    private function format(string $message, $data = null, array $errors = [], int $code = 0)
    {
        $response['code'] = $code;

        if ($message) {
            $response['message'] = $message;
        }

        if ($data) {
            $response['data'] = $data;
        }

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
