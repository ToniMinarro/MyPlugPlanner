<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Application\List;

use IberdrolaApi\Charge\Domain\Model\Charges;
use JsonSerializable;

final readonly class ListChargesResponse implements JsonSerializable
{
    private const string CHARGES = 'charges';
    private const string CHARGE_COUNT = 'chargeCount';
    private const string TOTAL_AMOUNT = 'totalAmount';
    private const string TOTAL_KWH_CHARGED = 'totalKwHCharged';

    private function __construct(
        private Charges $charges
    ) {
    }

    public static function createFromApiCharges(Charges $charges): self
    {
        return new self($charges);
    }

    public function jsonSerialize(): array
    {
        return [
            self::CHARGES => $this->charges,
            self::CHARGE_COUNT => $this->charges->count(),
            self::TOTAL_AMOUNT => $this->charges->totalAmount(),
            self::TOTAL_KWH_CHARGED => $this->charges->totalKwHCharged(),
        ];
    }
}
