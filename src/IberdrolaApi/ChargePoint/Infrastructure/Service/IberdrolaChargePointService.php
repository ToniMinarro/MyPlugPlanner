<?php

declare(strict_types=1);

namespace MyPlugPlanner\IberdrolaApi\ChargePoint\Infrastructure\Service;

use MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Service\ChargePointService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class IberdrolaChargePointService implements ChargePointService
{
    private const string GET_CONNECTION_POINT_INFO_ENDPOINT = '/o/webclipb/iberdrola/puntosrecargacontroller/getDatosPuntoRecarga';

    public function __construct(
        private readonly HttpClientInterface $iberdrolaApiClient
    ) {
    }

    public function getChargePointInfo(int $chargePointId): array
    {
        try {
            $request = $this->iberdrolaApiClient->request(
                Request::METHOD_POST,
                self::GET_CONNECTION_POINT_INFO_ENDPOINT,
                [
                    'json' => [
                        'dto' => [
                            'cuprId' => [$chargePointId]
                        ],
                        'language' => 'es'
                    ],
                ],
            );

            if ($request->getStatusCode() !== Response::HTTP_OK) {
                throw new \RuntimeException("Error al conectar con Iberdrola: " . $request->getStatusCode());
            }

            $response = json_decode(
                $request->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            return $response;
        } catch (Exception|TransportExceptionInterface $e) {
            return [];
        }
    }
}
