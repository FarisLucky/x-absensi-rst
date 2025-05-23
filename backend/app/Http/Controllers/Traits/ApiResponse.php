<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Prepare response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return
     */
    public function prepareApiResponse(string $message = '', int $statusCode = Response::HTTP_OK): array
    {
        if (empty($message)) {
            $message = Response::$statusTexts[$statusCode];
        }

        return [
            'message' => $message,
            'statusCode' => $statusCode,
        ];
    }

    /**
     * Success Response
     *
     * @param    $data
     * @param  int  $statusCode
     * @param  string  $message
     * @return JsonResponse
     */
    public function successApiResponse($data, int $statusCode = Response::HTTP_OK, string $message = ''): JsonResponse
    {
        $response = $this->prepareApiResponse($message, $statusCode);
        $response['data'] = $data;

        return response()->json($response, $statusCode);
    }

    /**
     * Error Response
     *
     * @param    $errors
     * @param  int  $statusCode
     * @param  string  $message
     * @return JsonResponse
     */
    public function errorApiResponse($errors = null, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, string $message = ''): JsonResponse
    {
        $response = $this->prepareApiResponse($message, $statusCode);
        $response['errors'] = $errors;
        Log::info('error', [$errors]);

        return response()->json($response, $statusCode);
    }

    /**
     * Response with status code 200.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function okApiResponse($data, string $message = ''): JsonResponse
    {
        return $this->successApiResponse($data, Response::HTTP_OK, $message);
    }

    /**
     * Response with status code 204.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function noContentApiResponse(string $message = ''): JsonResponse
    {
        return $this->successApiResponse('', Response::HTTP_NO_CONTENT, $message);
    }

    /**
     * Response with status code 201.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function createdApiResponse($data, string $message = ''): JsonResponse
    {
        return $this->successApiResponse($data, Response::HTTP_CREATED, $message);
    }

    /**
     * Response with status code 400.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function badRequestApiResponse($data, string $message = ''): JsonResponse
    {
        return $this->errorApiResponse($data, Response::HTTP_BAD_REQUEST, $message);
    }

    /**
     * Response with status code 401.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function unauthorizedApiResponse($data, string $message = ''): JsonResponse
    {
        return $this->errorApiResponse($data, Response::HTTP_UNAUTHORIZED, $message);
    }

    /**
     * Response with status code 403.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function forbiddenApiResponse($data, string $message = ''): JsonResponse
    {
        return $this->errorApiResponse($data, Response::HTTP_FORBIDDEN, $message);
    }

    /**
     * Response with status code 404.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function notFoundApiResponse($data, string $message = ''): JsonResponse
    {
        return $this->errorApiResponse($data, Response::HTTP_NOT_FOUND, $message);
    }

    /**
     * Response with status code 409.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function conflictApiResponse($data, string $message = ''): JsonResponse
    {
        return $this->errorApiResponse($data, Response::HTTP_CONFLICT, $message);
    }

    /**
     * Response with status code 422.
     *
     * @param    $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function unprocessableApiResponse($data, string $message = ''): JsonResponse
    {
        return $this->errorApiResponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }
}
