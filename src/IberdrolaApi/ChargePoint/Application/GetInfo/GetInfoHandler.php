<?php

declare(strict_types=1);

namespace IberdrolaApi\ChargePoint\Application\GetInfo;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use IberdrolaApi\ChargePoint\Domain\Service\ChargePointService;

#[AsMessageHandler]
readonly class GetInfoHandler
{
    public function __construct(private ChargePointService $chargePointService)
    {
    }

    public function __invoke(GetInfoQuery $query): array
    {
        return $this->chargePointService->getChargePointInfo(
            $query->chargePointId(),
        );
    }
}
