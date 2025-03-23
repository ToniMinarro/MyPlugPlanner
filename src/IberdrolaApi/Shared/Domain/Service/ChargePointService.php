<?php

declare(strict_types=1);

namespace IberdrolaApi\Shared\Domain\Service;

interface ChargePointService
{
    public function getChargePointInfo(int $chargePointId): array;
    public function listCharges(): array;
}
