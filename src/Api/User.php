<?php

namespace AlirezaProgrammerMaker\UnInsta\Api;

use AlirezaProgrammerMaker\UnInsta\Client\UnInstaClient;
use AlirezaProgrammerMaker\UnInsta\Support\Constants;
use AlirezaProgrammerMaker\UnInsta\Exceptions\ApiException;

class User
{
    protected UnInstaClient $client;

    public function __construct(UnInstaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get current authenticated user data
     */
    public function getCurrentUser(): array|false
    {
        try {
            $url = Constants::API_BASE_URL . Constants::ENDPOINT_CURRENT_USER;
            $response = $this->client->get($url);

            if (isset($response->body['user'])) {
                return $response->body['user'];
            }

            return false;
        } catch (\Throwable $e) {
            throw new ApiException('Failed to get current user: ' . $e->getMessage());
        }
    }

    /**
     * Get user data by username
     */
    public function getByUsername(string $username): array|false
    {
        try {
            // Store original User-Agent
            $originalUserAgent = $this->client->getHeaders()['User-Agent'];

            // Switch to desktop User-Agent
            $this->client->setUserAgent(Constants::WEB_USER_AGENT);

            $url = Constants::WEB_BASE_URL . '/api/v1' . Constants::ENDPOINT_USER_INFO;
            $response = $this->client->get($url, ['username' => $username]);

            // Restore original User-Agent
            $this->client->setUserAgent($originalUserAgent);

            if (isset($response->body['data']['user'])) {
                return $response->body['data']['user'];
            }

            return false;
        } catch (\Throwable $e) {
            if (isset($originalUserAgent)) {
                $this->client->setUserAgent($originalUserAgent);
            }
            throw new ApiException('Failed to get user by username: ' . $e->getMessage());
        }
    }

    /**
     * Get user data by user ID
     */
    public function getById(string $userId): array|false
    {
        try {
            $url = Constants::API_BASE_URL . "/users/{$userId}/info/";
            $response = $this->client->get($url);

            if (isset($response->body['user'])) {
                return $response->body['user'];
            }

            return false;
        } catch (\Throwable $e) {
            throw new ApiException('Failed to get user by ID: ' . $e->getMessage());
        }
    }

    /**
     * Search users by query
     */
    public function search(string $query): array|false
    {
        try {
            $url = Constants::API_BASE_URL . "/users/search/";
            $response = $this->client->get($url, ['q' => $query]);

            if (isset($response->body['users'])) {
                return $response->body['users'];
            }

            return false;
        } catch (\Throwable $e) {
            throw new ApiException('Failed to search users: ' . $e->getMessage());
        }
    }
}
