<?php

declare(strict_types=1);

namespace IberdrolaApi\ChargePoint\Domain\Service;

interface ChargePointService
{
    public function getChargePointInfo(int $chargePointId): array;
}
