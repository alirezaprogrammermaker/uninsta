<?php

namespace AlirezaProgrammerMaker\UnInsta\Api;

use AlirezaProgrammerMaker\UnInsta\Client\UnInstaClient;
use AlirezaProgrammerMaker\UnInsta\Support\Constants;
use AlirezaProgrammerMaker\UnInsta\Exceptions\ApiException;

class Post
{
    protected UnInstaClient $client;

    public function __construct(UnInstaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get user posts by username
     */
    public function getByUsername(string $username, int $count = 33, ?string $maxId = null): array|false
    {
        try {
            // Store original User-Agent
            $originalUserAgent = $this->client->getHeaders()['User-Agent'];

            // Switch to desktop User-Agent for this request
            $this->client->setUserAgent(Constants::WEB_USER_AGENT);

            // Construct URL: https://www.instagram.com/api/v1/feed/user/{username}/username/
            $url = Constants::WEB_BASE_URL . '/api/v1' . sprintf(Constants::ENDPOINT_USER_FEED, $username);

            $queryParams = [
                'count' => $count,
            ];

            if ($maxId !== null) {
                $queryParams['max_id'] = $maxId;
            }

            $response = $this->client->get($url, $queryParams);

            // Restore original User-Agent
            $this->client->setUserAgent($originalUserAgent);

            if (!isset($response->body['items'])) {
                return false;
            }

            $data = [
                'posts' => $response->body['items'],
            ];

            if (isset($response->body['next_max_id'])) {
                $data['max_id'] = $response->body['next_max_id'];
            }

            return $data;
        } catch (\Throwable $e) {
            // Restore User-Agent even on error
            if (isset($originalUserAgent)) {
                $this->client->setUserAgent($originalUserAgent);
            }
            throw new ApiException('Failed to get posts: ' . $e->getMessage());
        }
    }

    /**
     * Get post by media ID
     */
    public function getById(string $mediaId): array|false
    {
        try {
            // Store original User-Agent
            $originalUserAgent = $this->client->getHeaders()['User-Agent'];

            // Switch to desktop User-Agent
            $this->client->setUserAgent(Constants::WEB_USER_AGENT);

            $url = Constants::API_BASE_URL . "/media/{$mediaId}/info/";
            $response = $this->client->get($url);

            // Restore original User-Agent
            $this->client->setUserAgent($originalUserAgent);

            if (isset($response->body['items'][0])) {
                return $response->body['items'][0];
            }

            return false;
        } catch (\Throwable $e) {
            if (isset($originalUserAgent)) {
                $this->client->setUserAgent($originalUserAgent);
            }
            throw new ApiException('Failed to get post by ID: ' . $e->getMessage());
        }
    }

    /**
     * Get post comments
     */
    public function getComments(string $mediaId, ?string $maxId = null): array|false
    {
        try {
            // Store original User-Agent
            $originalUserAgent = $this->client->getHeaders()['User-Agent'];

            // Switch to desktop User-Agent
            $this->client->setUserAgent(Constants::WEB_USER_AGENT);

            $url = Constants::API_BASE_URL . "/media/{$mediaId}/comments/";

            $queryParams = [];
            if ($maxId !== null) {
                $queryParams['max_id'] = $maxId;
            }

            $response = $this->client->get($url, $queryParams);

            // Restore original User-Agent
            $this->client->setUserAgent($originalUserAgent);

            if (isset($response->body['comments'])) {
                $data = [
                    'comments' => $response->body['comments'],
                ];

                if (isset($response->body['next_max_id'])) {
                    $data['max_id'] = $response->body['next_max_id'];
                }

                return $data;
            }

            return false;
        } catch (\Throwable $e) {
            if (isset($originalUserAgent)) {
                $this->client->setUserAgent($originalUserAgent);
            }
            throw new ApiException('Failed to get comments: ' . $e->getMessage());
        }
    }

    /**
     * Get post likers
     */
    public function getLikers(string $mediaId, ?string $followRankingToken = null): array|false
    {
        try {
            $url = Constants::API_BASE_URL . "/media/{$mediaId}/likers/";

            $queryParams = [];
            if ($followRankingToken !== null) {
                $queryParams['follow_ranking_token'] = $followRankingToken;
            }

            $response = $this->client->get($url, $queryParams);

            if (isset($response->body['users'])) {
                $data = [
                    'users' => $response->body['users'],
                    'user_count' => $response->body['user_count'] ?? count($response->body['users']),
                ];

                // Add follow_ranking_token if present for pagination
                if (isset($response->body['follow_ranking_token'])) {
                    $data['follow_ranking_token'] = $response->body['follow_ranking_token'];
                }

                return $data;
            }

            return false;
        } catch (\Throwable $e) {
            throw new ApiException('Failed to get likers: ' . $e->getMessage());
        }
    }
}
