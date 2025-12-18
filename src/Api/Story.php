<?php

namespace AlirezaProgrammerMaker\UnInsta\Api;

use AlirezaProgrammerMaker\UnInsta\Client\UnInstaClient;
use AlirezaProgrammerMaker\UnInsta\Support\Constants;
use AlirezaProgrammerMaker\UnInsta\Exceptions\ApiException;

class Story
{
    protected UnInstaClient $client;

    public function __construct(UnInstaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get user stories by user ID
     */
    public function getByUserId(string $userId): array|false
    {
        try {
            // Store original User-Agent
            $originalUserAgent = $this->client->getHeaders()['User-Agent'];

            // Switch to desktop User-Agent for this request
            $this->client->setUserAgent(Constants::WEB_USER_AGENT);

            $url = Constants::WEB_BASE_URL . '/api/v1' . Constants::ENDPOINT_REELS_MEDIA;
            $response = $this->client->get($url, ['reel_ids' => $userId]);

            // Restore original User-Agent
            $this->client->setUserAgent($originalUserAgent);

            if (isset($response->body['reels_media']) && is_array($response->body['reels_media'])) {
                // Find stories for the requested user
                foreach ($response->body['reels_media'] as $reel) {
                    if (isset($reel['items']) && is_array($reel['items'])) {
                        return $reel['items'];
                    }
                }

                return []; // User found but has no stories
            }

            return false;
        } catch (\Throwable $e) {
            // Restore User-Agent even on error
            if (isset($originalUserAgent)) {
                $this->client->setUserAgent($originalUserAgent);
            }

            throw new ApiException('Failed to get user stories: ' . $e->getMessage());
        }
    }

    /**
     * Get multiple users' stories
     */
    public function getMultiple(array $userIds): array|false
    {
        try {
            $originalUserAgent = $this->client->getHeaders()['User-Agent'];
            $this->client->setUserAgent(Constants::WEB_USER_AGENT);

            $url = Constants::WEB_BASE_URL . '/api/v1' . Constants::ENDPOINT_REELS_MEDIA;
            $response = $this->client->get($url, ['reel_ids' => implode(',', $userIds)]);

            $this->client->setUserAgent($originalUserAgent);

            if (isset($response->body['reels_media'])) {
                return $response->body['reels_media'];
            }

            return false;
        } catch (\Throwable $e) {
            if (isset($originalUserAgent)) {
                $this->client->setUserAgent($originalUserAgent);
            }

            throw new ApiException('Failed to get multiple stories: ' . $e->getMessage());
        }
    }

    /**
     * Get story by media ID
     */
    public function getById(string $mediaId): array|false
    {
        try {
            $url = Constants::API_BASE_URL . "/media/{$mediaId}/info/";
            $response = $this->client->get($url);

            if (isset($response->body['items'][0])) {
                return $response->body['items'][0];
            }

            return false;
        } catch (\Throwable $e) {
            throw new ApiException('Failed to get story by ID: ' . $e->getMessage());
        }
    }
}
