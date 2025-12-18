<?php

namespace AlirezaProgrammerMaker\UnInsta\Client;

use GuzzleHttp\Client;
use AlirezaProgrammerMaker\UnInsta\Support\Constants;
use AlirezaProgrammerMaker\UnInsta\Support\ResponseHandler;

class UnInstaClient
{
    protected Client $client;
    protected array $headers;

    public function __construct(?string $sessionId = null, array $cookies = [], ?string $proxy = null)
    {
        $this->headers = Constants::getDefaultHeaders();

        $cookieArray = $cookies;
        if ($sessionId) {
            $cookieArray['sessionid'] = $sessionId;
        }

        // Create CookieJar
        // Using instagram.com as domain to match both i.instagram.com and www.instagram.com
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray($cookieArray, 'instagram.com');

        $config = [
            'headers' => $this->headers,
            'timeout' => 30,
            'cookies' => $jar,
            'allow_redirects' => [
                'max' => 10,
                'strict' => false,
                'referer' => true,
                'protocols' => ['https', 'http'],
                'track_redirects' => true
            ],
        ];

        if ($proxy) {
            $config['proxy'] = $proxy;
        }

        $this->client = new Client($config);
    }



    /**
     * Make GET request
     */
    public function get(string $endpoint, array $query = [], ?array $headers = null): object
    {
        $headers = $this->attachCsrfToken($headers ?? $this->headers);

        $response = $this->client->get($endpoint, [
            'query' => $query,
            'headers' => $headers,
        ]);

        return ResponseHandler::handle($response);
    }

    /**
     * Make POST request
     */
    public function post(string $endpoint, array $data = [], ?array $headers = null): object
    {
        $headers = $this->attachCsrfToken($headers ?? $this->headers);

        $response = $this->client->post($endpoint, [
            'form_params' => $data,
            'headers' => $headers,
        ]);

        return ResponseHandler::handle($response);
    }

    /**
     * Make OPTIONS request
     */
    public function options(string $endpoint, array $query = [], ?array $headers = null): object
    {
        $headers = $this->attachCsrfToken($headers ?? $this->headers);

        $response = $this->client->options($endpoint, [
            'query' => $query,
            'headers' => $headers,
        ]);

        return ResponseHandler::handle($response);
    }

    /**
     * Set session credentials
     */
    public function setSession(string $sessionId, array $cookies = [], ?string $proxy = null): void
    {
        $cookieArray = $cookies;
        if ($sessionId) {
            $cookieArray['sessionid'] = $sessionId;
        }

        // Create CookieJar
        $jar = \GuzzleHttp\Cookie\CookieJar::fromArray($cookieArray, 'instagram.com');

        // Remove manual Cookie header if present
        if (isset($this->headers['Cookie'])) {
            unset($this->headers['Cookie']);
        }

        $config = [
            // Use current headers but without hardcoded Cookie
            'headers' => $this->headers,
            'timeout' => 30,
            'cookies' => $jar,
            'allow_redirects' => [
                'max' => 10,
                'strict' => false,
                'referer' => true,
                'protocols' => ['https', 'http'],
                'track_redirects' => true
            ],
        ];

        if ($proxy) {
            $config['proxy'] = $proxy;
        }

        $this->client = new Client($config);
    }

    /**
     * Set User Agent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->headers['User-Agent'] = $userAgent;
    }

    /**
     * Get current headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get Guzzle client instance
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Attach CSRF token to headers
     */
    protected function attachCsrfToken(array $headers): array
    {
        // Try to find csrftoken in cookies
        $cookieJar = $this->client->getConfig('cookies');

        if ($cookieJar instanceof \GuzzleHttp\Cookie\CookieJarInterface) {
            $csrftoken = $cookieJar->getCookieByName('csrftoken');
            if ($csrftoken) {
                $headers['X-Csrftoken'] = $csrftoken->getValue();
            }
        }

        return $headers;
    }
}
