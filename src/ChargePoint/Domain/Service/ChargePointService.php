<?php

declare(strict_types=1);

namespace App\ChargePoint\Domain\Service;

interface ChargePointService
{
    public function getChargePointInfo(int $chargePointId): array;
}
