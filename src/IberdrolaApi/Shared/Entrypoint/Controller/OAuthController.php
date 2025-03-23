<?php

declare(strict_types=1);

namespace IberdrolaApi\Shared\Entrypoint\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function sprintf;

class OAuthController extends AbstractController
{
    private readonly HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route(path: '/login', name: 'oauth_login', methods: ['GET'])]
    public function login(Request $request): Response
    {
        $authorizeUrl = 'https://acc.iberdrola.com/wscoauth/oauth/authorize';
        $headersAuthorize = [
            'response_type' => 'code',
            'client_id' => 'RVZBQVBQ',
            'clientId-scope' => 'RVZBQVBQOk5EaWFlMjlUR3NqMG9pcDluTms3emRKV00xWkxRWStOV1FHOUszWW5kMGREbDZ0MTFYL0FaMDFGYlQ4UllxNlFzUk1GbERURExpNjU1WjFjQk04SjAvc0lYQUMwV0RUTUl6cUZKZVFRTGhRbXI5NGVYWHVrYWlNPQ==_YW50b25pb19qb3NlOTFAaG90bWFpbC5lczowMVNZMFNBbm1pNzE1Ng==',
            'redirect_uri' => 'https://www.iberdrola.es',
            'state' => '11',
            'versionApp' => 'ANDROID-4.28.11',
            'Plataforma' => 'Android',
            'User-Agent' => 'Iberdrola/4.28.10/Dalvik/2.1.0 (Linux; U; Android 13; M2101K6G Build/TKQ1.221013.002)',
            'Accept' => 'application/json',
            'c-rid' => '5f9255-a26-f8d-f0c-b174d167b',
            'deviceid' => 'cb5cfa93-db33-49c4-9a54-0b308de392e5',
            'Host' => 'acc.iberdrola.com',
            'Connection' => 'Keep-Alive',
            'Accept-Encoding' => 'gzip',
            'Accept-Language' => 'en',
            'content-length' => '0',
        ];

        $responseAuth = $this->client->request('GET', $authorizeUrl, [
            'headers' => $headersAuthorize,
        ]);

        $dataAuth = $responseAuth->toArray();

        $code = $dataAuth['code'] ?? null;

        if (!$code) {
            return new Response('Error: No se recibió el código de autorización.', Response::HTTP_BAD_REQUEST);
        }

        $accessTokenUrl = 'https://acc.iberdrola.com/wscoauth/oauth/access_token';
        $headersToken = [
            'versionApp' => 'ANDROID-4.28.11',
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
            'code' => $code,
            'grantType' => 'authorization_code',
            'redirectUrl' => 'https://www.iberdrola.es',
        ];

        $responseToken = $this->client->request('POST', $accessTokenUrl, [
            'headers' => $headersToken,
            'json' => $payload,
        ]);

        if (200 !== $responseToken->getStatusCode()) {
            return new Response(
                'Error al obtener token: ' . $responseToken->getStatusCode(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $dataToken = $responseToken->toArray();

        $accessToken = $dataToken['access_token'] ?? null;
        $refreshToken = $dataToken['refresh_token'] ?? null;
        $expiresIn = $dataToken['expires_in'] ?? null;

        if (!$accessToken) {
            return new Response('Error: No se obtuvo el access token.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $session = $request->getSession();
        $session->set('refresh_token', $refreshToken);

        return new Response(
            sprintf(
                "Access Token: %s\nRefresh Token: %s\nExpires In: %s segundos",
                $accessToken,
                $refreshToken,
                $expiresIn
            )
        );
    }

    #[Route(path: '/refresh-token', name: 'refresh_token', methods: ['GET'])]
    public function refreshToken(Request $request): Response
    {
        $session = $request->getSession();
        $storedRefreshToken = $session->get('refresh_token');

        if (!$storedRefreshToken) {
            return new Response(
                'No se encontró un refresh token. Inicia sesión nuevamente.',
                Response::HTTP_BAD_REQUEST
            );
        }

        $accessTokenUrl = 'https://acc.iberdrola.com/wscoauth/oauth/access_token';
        $headers = [
            'versionApp' => 'ANDROID-4.28.11',
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
            'refreshToken' => $storedRefreshToken,
        ];

        $response = $this->client->request('POST', $accessTokenUrl, [
            'headers' => $headers,
            'json' => $payload,
        ]);

        if (200 !== $response->getStatusCode()) {
            return new Response(
                'Error al refrescar token: ' . $response->getStatusCode(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $data = $response->toArray();

        $newAccessToken = $data['access_token'] ?? null;
        $newRefreshToken = $data['refresh_token'] ?? null;
        $expiresIn = $data['expires_in'] ?? null;

        if (!$newAccessToken) {
            return new Response('Error: No se obtuvo el nuevo access token.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $session->set('refresh_token', $newRefreshToken);

        return new Response(
            sprintf(
                "Nuevo Access Token: %s\nNuevo Refresh Token: %s\nExpires In: %s segundos",
                $newAccessToken,
                $newRefreshToken,
                $expiresIn
            )
        );
    }
}
