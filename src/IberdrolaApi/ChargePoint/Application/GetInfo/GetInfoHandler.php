<?php

declare(strict_types=1);

namespace MyPlugPlanner\IberdrolaApi\ChargePoint\Application\GetInfo;

use MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Service\ChargePointService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

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
