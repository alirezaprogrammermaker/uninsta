<?php

namespace AlirezaProgrammerMaker\UnInsta\Session;

use AlirezaProgrammerMaker\UnInsta\Support\Constants;
use AlirezaProgrammerMaker\UnInsta\Exceptions\SessionNotFoundException;

class SessionManager
{
    protected static string $storagePath = Constants::SESSION_STORAGE_PATH;

    /**
     * Set custom storage path
     */
    public static function setStoragePath(string $path): void
    {
        self::$storagePath = $path;
    }

    /**
     * Get storage path
     */
    public static function getStoragePath(): string
    {
        return self::$storagePath;
    }

    /**
     * Save session with cookies
     */
    public static function save(
        string $username, 
        string $sessionId, 
        array $headers, 
        ?string $userId = null
    ): void {
        if (!is_dir(self::$storagePath)) {
            mkdir(self::$storagePath, 0755, true);
        }

        $cookies = self::extractCookies($headers);
        $csrfTokenExpiry = self::extractCsrfExpiry($headers);

        $session = [
            'username' => $username,
            'user_id' => $userId,
            'sessionId' => $sessionId,
            'cookies' => $cookies,
            'csrfToken_expiry' => $csrfTokenExpiry,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $sessionPath = self::getSessionPath($username);
        file_put_contents($sessionPath, json_encode($session, JSON_PRETTY_PRINT));
    }

    /**
     * Get session by session ID
     */
    public static function getBySessionId(string $sessionId): ?array
    {
        $sessionFiles = glob(self::$storagePath . "/*.json");
        
        foreach ($sessionFiles as $sessionFile) {
            $content = file_get_contents($sessionFile);
            $session = json_decode($content, true);
            
            if ($session['sessionId'] === $sessionId) {
                return $session;
            }
        }

        return null;
    }

    /**
     * Get session by username
     */
    public static function getByUsername(string $username): ?array
    {
        $sessionPath = self::getSessionPath($username);

        if (!file_exists($sessionPath)) {
            return null;
        }

        $content = file_get_contents($sessionPath);
        return json_decode($content, true);
    }

    /**
     * Delete session by username
     */
    public static function delete(string $username): bool
    {
        $sessionPath = self::getSessionPath($username);

        if (file_exists($sessionPath)) {
            return unlink($sessionPath);
        }

        return false;
    }

    /**
     * Check if session exists
     */
    public static function exists(string $username): bool
    {
        return file_exists(self::getSessionPath($username));
    }

    /**
     * Get all sessions
     */
    public static function all(): array
    {
        $sessionFiles = glob(self::$storagePath . "/*.json");
        $sessions = [];

        foreach ($sessionFiles as $sessionFile) {
            $content = file_get_contents($sessionFile);
            $sessions[] = json_decode($content, true);
        }

        return $sessions;
    }

    /**
     * Extract cookies from headers
     */
    protected static function extractCookies(array $headers): array
    {
        $cookies = [];

        if (isset($headers['Set-Cookie'])) {
            foreach ($headers['Set-Cookie'] as $cookieString) {
                preg_match('/^([^=]+)=([^;]+);/', $cookieString, $matches);
                
                if (isset($matches[1], $matches[2])) {
                    $cookies[$matches[1]] = $matches[2];
                }
            }
        }

        return $cookies;
    }

    /**
     * Extract CSRF token expiry from headers
     */
    protected static function extractCsrfExpiry(array $headers): ?string
    {
        if (isset($headers['Set-Cookie'])) {
            foreach ($headers['Set-Cookie'] as $cookieString) {
                if (str_contains($cookieString, 'csrftoken') 
                    && preg_match('/expires=([^;]+)/i', $cookieString, $matches)) {
                    return $matches[1];
                }
            }
        }

        return null;
    }

    /**
     * Get session file path
     */
    protected static function getSessionPath(string $username): string
    {
        return self::$storagePath . "/" . $username . ".json";
    }
}