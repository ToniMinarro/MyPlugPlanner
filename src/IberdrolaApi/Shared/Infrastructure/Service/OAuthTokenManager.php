<?php

declare(strict_types=1);

namespace IberdrolaApi\Shared\Infrastructure\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OAuthTokenManager
{
    private int $refreshThreshold = 60;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly HttpClientInterface $client,
    ) {
    }

    public function getAccessToken(): mixed
    {
        $accessToken = $this->requestStack->getSession()->get('access_token');
        $expiresAt = $this->requestStack->getSession()->get('access_token_expires_at');

        if ($accessToken && $expiresAt && $expiresAt > (time() + $this->refreshThreshold)) {
            return $accessToken;
        }

        $newTokens = $this->refreshAccessToken();
        return $newTokens['access_token'] ?? null;
    }

    public function refreshAccessToken(): ?array
    {
        $refreshToken = $this->requestStack->getSession()->get('refresh_token');

        if (!$refreshToken) {
            return null;
        }

        $accessTokenUrl = 'https://acc.iberdrola.com/wscoauth/oauth/access_token';
        $headers = [
            'versionApp' => 'ANDROID-4.28.10',
            'Plataforma' => 'Android',
            'User-Agent' => 'Iberdrola/4.28.10/Dalvik/2.1.0 (Linux; U; Android 13; M2101K6G Build/TKQ1.221013.002)',
            'Accept' => 'application/json',
            'c-rid' => '5f9255-a26-f8d-f0c-b174d167b',
            'deviceid' => 'cb5cfa93-db33-49c4-9a54-0b308de392e5',
            'Content-Type' => 'application/json; charset=UTF-8',
            'Connection' => 'Keep-Alive',
            'Accept-Encoding' => 'gzip',
            'Accept-Language' => 'en',
        ];

        $payload = [
            'clientId' => 'RVZBQVBQ=',
            'grantType' => 'refresh_token',
            'refreshToken' => $refreshToken,
        ];

        $response = $this->client->request('POST', $accessTokenUrl, [
            'headers' => $headers,
            'json' => $payload,
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->toArray();

        $newAccessToken = $data['access_token'] ?? null;
        $newRefreshToken = $data['refresh_token'] ?? null;
        $expiresIn = $data['expires_in'] ?? null;

        if ($newAccessToken && $expiresIn) {
            $expiresAt = time() + (int) $expiresIn;
            $this->requestStack->getSession()->set('access_token', $newAccessToken);
            $this->requestStack->getSession()->set('access_token_expires_at', $expiresAt);

            if ($newRefreshToken) {
                $this->requestStack->getSession()->set('refresh_token', $newRefreshToken);
            }
            return $data;
        }

        return null;
    }
}
