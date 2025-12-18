<?php

namespace AlirezaProgrammerMaker\UnInsta;

use AlirezaProgrammerMaker\UnInsta\Client\UnInstaClient;
use AlirezaProgrammerMaker\UnInsta\Session\SessionManager;
use AlirezaProgrammerMaker\UnInsta\Api\User;
use AlirezaProgrammerMaker\UnInsta\Api\Post;
use AlirezaProgrammerMaker\UnInsta\Api\Story;
use AlirezaProgrammerMaker\UnInsta\Api\Highlight;
use AlirezaProgrammerMaker\UnInsta\Support\Constants;
use AlirezaProgrammerMaker\UnInsta\Exceptions\AuthenticationException;

class UnInsta extends UnInstaClient
{
    protected User $userApi;
    protected Post $postApi;
    protected Story $storyApi;
    protected Highlight $highlightApi;

    public function __construct(?string $sessionId = null, array $cookies = [], ?string $proxy = null)
    {
        parent::__construct($sessionId, $cookies, $proxy);

        // Initialize API classes
        $this->userApi = new User($this);
        $this->postApi = new Post($this);
        $this->storyApi = new Story($this);
        $this->highlightApi = new Highlight($this);
    }

    /**
     * Get package version
     */
    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Login with session ID
     */
    public function loginWithSessionId(string $sessionId): array|false
    {
        try {
            $session = SessionManager::getBySessionId($sessionId);

            if (!$session) {
                $this->setSession($sessionId, []);
                $currentUser = $this->userApi->getCurrentUser();

                if ($currentUser) {
                    $response = $this->get(Constants::API_BASE_URL . Constants::ENDPOINT_CURRENT_USER);

                    SessionManager::save(
                        $currentUser['username'],
                        $sessionId,
                        $response->headers,
                        $currentUser['pk']
                    );

                    $session = SessionManager::getBySessionId($sessionId);
                    $this->setSession($sessionId, $session['cookies']);

                    return $currentUser;
                }

                return false;
            } else {
                $this->setSession($sessionId, $session['cookies']);
                return $this->userApi->getCurrentUser();
            }
        } catch (\Throwable $e) {
            throw new AuthenticationException('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(): array|false
    {
        return $this->userApi->getCurrentUser();
    }

    /**
     * Get user by username
     */
    public function getUserByUsername(string $username): array|false
    {
        return $this->userApi->getByUsername($username);
    }

    /**
     * Get user by ID
     */
    public function getUserById(string $userId): array|false
    {
        return $this->userApi->getById($userId);
    }

    /**
     * Search users
     */
    public function searchUsers(string $query): array|false
    {
        return $this->userApi->search($query);
    }

    /**
     * Get user posts
     */
    public function getPosts(string $username, int $count = 33, ?string $maxId = null): array|false
    {
        return $this->postApi->getByUsername($username, $count, $maxId);
    }

    /**
     * Get post by ID
     */
    public function getPostById(string $mediaId): array|false
    {
        return $this->postApi->getById($mediaId);
    }

    /**
     * Get post comments
     */
    public function getPostComments(string $mediaId, ?string $maxId = null): array|false
    {
        return $this->postApi->getComments($mediaId, $maxId);
    }

    /**
     * Get post likers
     */
    public function getPostLikers(string $mediaId, ?string $followRankingToken = null): array|false
    {
        return $this->postApi->getLikers($mediaId, $followRankingToken);
    }

    /**
     * Get user stories
     */
    public function getUserStories(string $userId): array|false
    {
        return $this->storyApi->getByUserId($userId);
    }

    /**
     * Get multiple users' stories
     */
    public function getMultipleStories(array $userIds): array|false
    {
        return $this->storyApi->getMultiple($userIds);
    }

    /**
     * Get story by ID
     */
    public function getStoryById(string $mediaId): array|false
    {
        return $this->storyApi->getById($mediaId);
    }

    /**
     * Get user highlights
     */
    public function getUserHighlights(string $userId): array|false
    {
        return $this->highlightApi->getByUserId($userId);
    }

    /**
     * Get highlight media
     */
    public function getHighlightMedia(string $highlightId): array|false
    {
        return $this->highlightApi->getMediaById($highlightId);
    }

    /**
     * Get multiple highlights
     */
    public function getMultipleHighlights(array $highlightIds): array|false
    {
        return $this->highlightApi->getMultiple($highlightIds);
    }

    /**
     * Set custom session storage path
     */
    public function setSessionStoragePath(string $path): void
    {
        SessionManager::setStoragePath($path);
    }
}
