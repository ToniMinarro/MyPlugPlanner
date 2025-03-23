<?php

declare(strict_types=1);

namespace IberdrolaApi\Shared\Infrastructure\Service;

use Exception;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use IberdrolaApi\Shared\Domain\Service\ChargePointService;

final class IberdrolaServiceTest extends TestCase
{
    private ChargePointService $service;
    private OAuthTokenManager $tokenManager;
    private HttpClientInterface $httpClientMock;

    protected function setup(): void
    {
        parent::setUp();

        $this->tokenManager = $this->createMock(OAuthTokenManager::class);
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->service = new IberdrolaService(
            $this->tokenManager,
            $this->httpClientMock,
            $this->httpClientMock,
        );
    }

    public function test_get_charge_point_info_ok_response(): void
    {
        $expectedResponseBody = $this->okResponseBody();

        $this->tokenManager
            ->expects($this->never())
            ->method('getAccessToken');

        $this->httpClientMock
            ->method('request')
            ->willReturn(
                $this->createResponseMock(
                    $this->okResponseBody(),
                    Response::HTTP_OK,
                ),
            );

        $chargePointInfo = $this->service->getChargePointInfo(1);

        $this->assertIsArray($chargePointInfo);
        $this->assertEquals($expectedResponseBody, $chargePointInfo);
    }

    public function test_get_charge_point_info_bad_response(): void
    {
        $expectedResponseBody = [];

        $this->tokenManager
            ->expects($this->never())
            ->method('getAccessToken');

        $this->httpClientMock
            ->method('request')
            ->willReturn(
                $this->createResponseMock(
                    $this->badResponseBody(),
                    Response::HTTP_BAD_REQUEST,
                ),
            );

        self::expectException(RuntimeException::class);

        $chargePointInfo = $this->service->getChargePointInfo(1);

        $this->assertIsArray($chargePointInfo);
        $this->assertEmpty($chargePointInfo);
        $this->assertEquals($expectedResponseBody, $chargePointInfo);
    }

    public function test_get_charge_point_info_bad_response2(): void
    {
        $expectedResponseBody = [];

        $this->tokenManager
            ->expects($this->never())
            ->method('getAccessToken');

        $this->httpClientMock
            ->method('request')
            ->willReturn(
                $this->createBadResponseMock(),
            );

        $chargePointInfo = $this->service->getChargePointInfo(1);

        $this->assertIsArray($chargePointInfo);
        $this->assertEmpty($chargePointInfo);
        $this->assertEquals($expectedResponseBody, $chargePointInfo);
    }

    public function test_get_charge_point_info_server_error_response(): void
    {
        $expectedResponseBody = [];

        $this->tokenManager
            ->expects($this->never())
            ->method('getAccessToken');

        $this->httpClientMock
            ->method('request')
            ->willThrowException(new Exception());

        self::expectException(Exception::class);

        $chargePointInfo = $this->service->getChargePointInfo(1);

        $this->assertIsArray($chargePointInfo);
        $this->assertEmpty($chargePointInfo);
        $this->assertEquals($expectedResponseBody, $chargePointInfo);
    }

    private function createResponseMock(array $responseBody, int $statusCode): ResponseInterface
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn($statusCode);

        $responseMock
            ->method('getContent')
            ->willReturn(json_encode($responseBody));

        return $responseMock;
    }

    private function createBadResponseMock(): ResponseInterface
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn(Response::HTTP_OK);

        $responseMock
            ->method('getContent')
            ->willReturn('null');

        return $responseMock;
    }

    private function okResponseBody(): array
    {
        return [
            'seguro' => false,
            'errorAjax' => null,
            'tiempo' => 1740824902100,
            'seg' => null,
            'entidad' => [
                [
                    'locationData' => [
                        'cuprReservationIndicator' => true,
                        'energyLimitSupported' => true,
                        'level' => '0',
                        'latitude' => 43.411771,
                        'cuprName' => 'E.S. AVIA BAKIO - 01',
                        'lstAdditionalServices' => [],
                        'situationCode' => 'OPER',
                        'operator' => [
                            'operatorDesc' => 'Iberdrola',
                        ],
                        'accessType' => [
                            'accessCodeType' => null,
                            'accessTypeDesc' => null,
                        ],
                        'number' => 'S/N',
                        'lastNotification' => [
                            'notificationDescription' => null,
                            'notificationId' => 1,
                        ],
                        'isInstalledInParking' => null,
                        'scheduleType' => [
                            'scheduleTypeDesc' => '24/7',
                            'scheduleCodeType' => 'VEINTICUATRO_HORAS',
                        ],
                        'cuprRoleId' => 2,
                        'chargePointTypeCode' => 'P',
                        'chargePointPicture' => [
                            'lastModificationDate' => '2023-10-06T13:13:22.000+00:00',
                        ],
                        'cuprId' => 98807,
                        'lstSchedules' => [],
                        'supplyPointData' => [
                            'cpAddress' => [
                                'streetName' => 'ZINTADUIKO JARDUERA GUNEA',
                                'townName' => 'BAKIO',
                                'regionName' => 'BIZKAIA',
                                'streetNum' => '9',
                            ],
                        ],
                        'favorite' => false,
                        'longitude' => -2.814704,
                        'additionalInfoAddress' => null,
                    ],
                    'advantageous' => true,
                    'emergencyStopButtonPressed' => false,
                    'serialNumber' => '62217017120004',
                    'cpId' => 132908,
                    'messageKeys' => null,
                    'sameCourtesyTime' => true,
                    'logicalSocket' => [
                        [
                            'evseId' => null,
                            'parkingRestriction' => [],
                            'physicalSocket' => [
                                [
                                    'socketType' => [
                                        'socketName' => 'Combo-Tipo2',
                                        'socketTypeId' => '7',
                                    ],
                                    'appliedRate' => [
                                        'tariffStatus' => null,
                                        'recharge' => [
                                            'typeRate' => 'pr',
                                            'price' => 0.45,
                                            'finalPrice' => 0.45,
                                        ],
                                        'reservation' => [
                                            'typeRate' => 'pr',
                                            'price' => 1,
                                            'finalPrice' => 1,
                                        ],
                                        'remainingKwhSuscription' => null,
                                    ],
                                    'physicalSocketId' => 223080,
                                    'physicalSocketCode' => '1',
                                    'maxPower' => 50,
                                    'status' => [
                                        'updateDate' => '2025-03-01T10:23:48.000+00:00',
                                        'statusId' => 2,
                                        'statusCode' => 'OCCUPIED',
                                    ],
                                    'chargeSpeed' => [
                                        'chargeSpeedId' => 3,
                                    ],
                                ],
                            ],
                            'chargeSpeedId' => 3,
                            'reservationInProgress' => false,
                            'logicalSocketId' => 211475,
                            'status' => [
                                'updateDate' => '2025-03-01T10:23:48.000+00:00',
                                'statusId' => 2,
                                'statusCode' => 'OCCUPIED',
                            ],
                            'logicalSocketCode' => null,
                        ],
                    ],
                    'creationDate' => '2022-11-18T11:31:49.000+00:00',
                    'fullRequired' => false,
                    'chargePointInternalId' => '62217017120004',
                    'cpStatus' => [
                        'updateDate' => null,
                        'statusId' => 2,
                        'statusCode' => 'OCCUPIED',
                    ],
                ],
            ],
            'errores' => null,
            'serviceException' => null,
        ];
    }

    private function badResponseBody(): array
    {
        return [
            'seguro' => false,
            'errorAjax' => null,
            'tiempo' => 1740824902100,
            'seg' => null,
            'entidad' => [],
            'errores' => null,
            'serviceException' => null,
        ];
    }
}
