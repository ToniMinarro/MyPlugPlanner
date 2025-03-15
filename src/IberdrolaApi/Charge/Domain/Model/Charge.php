<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Domain\Model;

use PcComponentes\Ddd\Domain\Model\ValueObject\DateTimeValueObject;

final class Charge
{
    private const string CHARGE_ID = 'chargeId';
    private const string AMOUNT = 'amount';
    private const string FINAL_AMOUNT = 'finalAmount';
    private const string TOTAL_KWH_CHARGED = 'totalKwhCharged';
    private const string OCCURRED_ON = 'occurredOn';
    private const string NAME = 'charge';

    protected function __construct(
        private(set) int $chargeId { get => $this->chargeId; },
        private(set) float $amount { get => $this->amount; },
        private(set) float $finalAmount { get => $this->finalAmount; },
        private(set) float $totalKwhCharged { get => $this->totalKwhCharged; },
        private(set) DateTimeValueObject $occurredOn { get => $this->occurredOn; },
    )
    {
    }

    public static function from(array $chargeData): self
    {
        return new self(
            $chargeData[self::CHARGE_ID],
            $chargeData[self::AMOUNT],
            $chargeData[self::FINAL_AMOUNT],
            $chargeData[self::TOTAL_KWH_CHARGED],
            DateTimeValueObject::from($chargeData[self::OCCURRED_ON]),
        );
    }

    public static function modelName(): string
    {
        return self::NAME;
    }

    public function jsonSerialize(): array
    {
        return [
            self::CHARGE_ID => $this->chargeId,
        ];
    }
}
