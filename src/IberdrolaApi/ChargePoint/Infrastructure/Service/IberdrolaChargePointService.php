<?php

declare(strict_types=1);

namespace IberdrolaApi\ChargePoint\Infrastructure\Service;

use Exception;
use IberdrolaApi\ChargePoint\Domain\Service\ChargePointService;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class IberdrolaChargePointService implements ChargePointService
{
    private const string GET_CONNECTION_POINT_INFO_ENDPOINT = '/o/webclipb/iberdrola/puntosrecargacontroller/getDatosPuntoRecarga';

    public function __construct(
        private HttpClientInterface $iberdrolaApiClient
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
                            'cuprId' => [$chargePointId],
                        ],
                        'language' => 'es',
                    ],
                ],
            );

            if ($request->getStatusCode() !== Response::HTTP_OK) {
                throw new RuntimeException('Error al conectar con Iberdrola: ' . $request->getStatusCode());
            }

            return json_decode(
                $request->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Exception|TransportExceptionInterface) {
            return [];
        }
    }
}
