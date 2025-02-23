<?php

declare(strict_types=1);

namespace MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Service;

interface ChargePointService
{
    public function getChargePointInfo(int $chargePointId): array;
}
