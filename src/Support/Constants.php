<?php

namespace AlirezaProgrammerMaker\UnInsta\Support;

class Constants
{
    /**
     * User Agent for Web requests
     */
    public const WEB_USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.0.0';

    /**
     * User Agent for Mobile requests
     */
    public const MOBILE_USER_AGENT = 'Instagram 155.0.0.37.107 Android (23/6.0.1; 640dpi; 1440x2560; samsung; SM-G930F; herolte; samsungexynos8890; en_US; 239490550)';

    /**
     * Instagram API Base URLs
     */
    public const API_BASE_URL = 'https://i.instagram.com/api/v1';
    public const WEB_BASE_URL = 'https://www.instagram.com';
    public const GRAPH_BASE_URL = 'https://www.instagram.com/graphql/query/';

    /**
     * API Endpoints
     */
    public const ENDPOINT_CURRENT_USER = '/accounts/current_user/';
    public const ENDPOINT_USER_INFO = '/users/web_profile_info/';
    public const ENDPOINT_USER_FEED = '/feed/user/%s/username/';
    public const ENDPOINT_REELS_MEDIA = '/feed/reels_media/';

    /**
     * Default Headers
     */
    public static function getDefaultHeaders(): array
    {
        return [
            'User-Agent' => self::MOBILE_USER_AGENT,
            'Accept' => '*/*',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Origin' => self::WEB_BASE_URL,
            'Referer' => self::WEB_BASE_URL . '/',
            'Priority' => 'u=1, i',
            'Sec-CH-Prefers-Color-Scheme' => 'light',
            'Sec-CH-UA' => '"Microsoft Edge";v="125", "Chromium";v="125", "Not.A/Brand";v="24"',
            'Sec-CH-UA-Full-Version-List' => '"Microsoft Edge";v="125.0.2535.92", "Chromium";v="125.0.6422.142", "Not.A/Brand";v="24.0.0.0"',
            'Sec-CH-UA-Mobile' => '?0',
            'Sec-CH-UA-Model' => '',
            'Sec-CH-UA-Platform' => 'Windows',
            'Sec-CH-UA-Platform-Version' => '14.0.0',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'X-Asbd-Id' => '129477',
            'X-Bloks-Version-Id' => 'e2004666934296f275a5c6b2c9477b63c80977c7cc0fd4b9867cb37e36092b68',
            'X-Fb-Friendly-Name' => 'PolarisProfilePageContentDirectQuery',
            'X-Fb-Lsd' => 'M3NO8DGntYMUXbxC0LEtjD',
            'X-Ig-App-Id' => '936619743392459',
        ];
    }

    /**
     * Default session storage path
     */
    public const SESSION_STORAGE_PATH = 'uninsta/sessions';
}
