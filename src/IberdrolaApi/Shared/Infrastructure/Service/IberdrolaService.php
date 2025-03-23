<?php

declare(strict_types=1);

namespace IberdrolaApi\Shared\Infrastructure\Service;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use IberdrolaApi\Shared\Domain\Service\ChargePointService;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use function is_array;

final readonly class IberdrolaService implements ChargePointService
{
    private const string GET_CONNECTION_POINT_INFO_ENDPOINT = '/o/webclipb/iberdrola/puntosrecargacontroller/getDatosPuntoRecarga';
    private const string LIST_CHARGES_ENDPOINT = '/vecomges/api/appuseroperation/getListMovements';

    public function __construct(
        private OAuthTokenManager $tokenManager,
        private HttpClientInterface $iberdrolaApiClient,
        private HttpClientInterface $iberdrolaPublicApiClient
    ) {
    }

    public function getChargePointInfo(int $chargePointId): array
    {
        try {
            $request = $this->iberdrolaPublicApiClient->request(
                Request::METHOD_POST,
                self::GET_CONNECTION_POINT_INFO_ENDPOINT,
                [
                    'json' => [
                        'dto' => [
                            'cuprId' => [$chargePointId],
                        ],
                        'language' => 'es',
                    ],
                ],
            );

            if ($request->getStatusCode() !== Response::HTTP_OK) {
                throw new RuntimeException('Error al conectar con Iberdrola: ' . $request->getStatusCode());
            }

            $response = json_decode(
                $request->getContent(),
                true,
            );

            if (!is_array($response)) {
                return [];
            }

            return $response;
        } catch (
            ClientExceptionInterface
            |ServerExceptionInterface
            |TransportExceptionInterface
            |RedirectionExceptionInterface
        ) {
            return [];
        }
    }

    public function listCharges(): array
    {
        try {
            $accessToken = $this->tokenManager->getAccessToken();
            if (!$accessToken || !is_string($accessToken)) {
                return [
                    'No se pudo obtener un access token. Requiere autenticación.',
                    Response::HTTP_UNAUTHORIZED
                ];
            }

            $request = $this->iberdrolaApiClient->request(
                Request::METHOD_POST,
                self::LIST_CHARGES_ENDPOINT,
                [
                    'headers' => [
                        'Authorization' => sprintf('Bearer %s', $accessToken),
                    ],
                    'json' => [
                        'invoiceAvailable' => false,
                        'movementList' => [],
                        'payMethods' => [],
                        'selectedPage' => 0,
                    ],
                ],
            );

            if ($request->getStatusCode() !== Response::HTTP_OK) {
                throw new RuntimeException('Error al conectar con Iberdrola: ' . json_encode($request->getInfo()));
            }

            $response = json_decode(
                $request->getContent(),
                true,
            );

            if (!is_array($response)) {
                return [];
            }

            return $response;
        } catch (
            ClientExceptionInterface
            |ServerExceptionInterface
            |TransportExceptionInterface
            |RedirectionExceptionInterface
        ) {
            return [];
        }
    }
}
