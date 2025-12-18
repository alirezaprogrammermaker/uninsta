<?php

namespace AlirezaProgrammerMaker\UnInsta\Support;

use Psr\Http\Message\ResponseInterface;

class ResponseHandler
{
    /**
     * Process HTTP response and return structured data
     */
    public static function handle(ResponseInterface $response): object
    {
        $headers = $response->getHeaders();
        $body = json_decode($response->getBody()->getContents(), true);

        return (object)[
            'body' => $body,
            'headers' => $headers,
            'statusCode' => $response->getStatusCode(),
        ];
    }

    /**
     * Extract CSRF token from headers
     */
    public static function extractCsrfToken(array $headers): ?string
    {
        return $headers['x-csrftoken'][0] ?? null;
    }

    /**
     * Check if response is successful
     */
    public static function isSuccessful(object $response): bool
    {
        if (isset($response->body['status'])) {
            return $response->body['status'] === 'ok';
        }

        return $response->statusCode >= 200 && $response->statusCode < 300;
    }

    /**
     * Get error message from response
     */
    public static function getErrorMessage(object $response): ?string
    {
        return $response->body['message'] 
            ?? $response->body['error_title']
            ?? 'Unknown error occurred';
    }
}