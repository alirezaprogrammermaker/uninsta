<?php

namespace AlirezaProgrammerMaker\UnInsta\Api;

use AlirezaProgrammerMaker\UnInsta\Client\UnInstaClient;
use AlirezaProgrammerMaker\UnInsta\Support\Constants;
use AlirezaProgrammerMaker\UnInsta\Exceptions\ApiException;

class Highlight
{
    protected UnInstaClient $client;

    public function __construct(UnInstaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get user highlights by user ID
     */
    public function getByUserId(string $userId): array|false
    {
        try {
            // Store original User-Agent
            $originalUserAgent = $this->client->getHeaders()['User-Agent'];

            // Switch to desktop User-Agent for this request
            $this->client->setUserAgent(Constants::WEB_USER_AGENT);

            $queryParams = [
                'query_hash' => 'd4d88dc1500312af6f937f7b804c68c3',
                'user_id' => $userId,
                'include_chaining' => 'false',
                'include_reel' => 'false',
                'include_suggested_users' => 'false',
                'include_logged_out_extras' => 'false',
                'include_live_status' => 'false',
                'include_highlight_reels' => 'true',
            ];

            $response = $this->client->get(Constants::GRAPH_BASE_URL, $queryParams);

            // Restore original User-Agent
            $this->client->setUserAgent($originalUserAgent);

            if (isset($response->body['status']) && $response->body['status'] === 'ok') {
                return $response->body['data']['user']['edge_highlight_reels']['edges'] ?? [];
            }

            return false;
        } catch (\Throwable $e) {
            // Restore User-Agent even on error
            if (isset($originalUserAgent)) {
                $this->client->setUserAgent($originalUserAgent);
            }

            throw new ApiException('Failed to get user highlights: ' . $e->getMessage());
        }
    }

    /**
     * Get highlight media by highlight ID
     */
    public function getMediaById(string $highlightId): array|false
    {
        try {
            $highlightKey = "highlight:{$highlightId}";

            $data = [
                'user_ids' => json_encode([$highlightKey]),
                'source' => 'profile',
            ];

            $url = Constants::API_BASE_URL . Constants::ENDPOINT_REELS_MEDIA;
            $response = $this->client->post($url, $data);

            if (isset($response->body['reels'][$highlightKey])) {
                return $response->body;
            }

            return false;
        } catch (\Throwable $e) {
            throw new ApiException('Failed to get highlight media: ' . $e->getMessage());
        }
    }

    /**
     * Get multiple highlights
     */
    public function getMultiple(array $highlightIds): array|false
    {
        try {
            $formattedIds = array_map(fn($id) => "highlight:{$id}", $highlightIds);

            $data = [
                'user_ids' => json_encode($formattedIds),
                'source' => 'profile',
            ];

            $url = Constants::API_BASE_URL . Constants::ENDPOINT_REELS_MEDIA;
            $response = $this->client->post($url, $data);

            if (isset($response->body['reels'])) {
                return array_values($response->body);
            }

            return false;
        } catch (\Throwable $e) {
            throw new ApiException('Failed to get multiple highlights: ' . $e->getMessage());
        }
    }
}
