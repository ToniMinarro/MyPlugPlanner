<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Application\List\Dto;

use JsonSerializable;
use RuntimeException;
use PcComponentes\Ddd\Domain\Model\ValueObject\DateTimeValueObject;

final class ApiChargeDto implements JsonSerializable
{
    private const string API_MOVEMENT_DATE = 'movementDate';
    private const string API_FINAL_PRICE = 'finalPrice';
    private const string API_RECHARGE = 'recharge';
    private const string API_RECHARGE_ID = 'rechargeId';
    private const string API_SESSION_ID = 'sessionId';
    private const string API_KWH_CONSUMED = 'kwhConsumed';
    private const string API_FINAL_MOVEMENTS_PRICE = 'finalMovementsPrice';
    private const string API_DEFAULT_DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    private const string DOMAIN_CHARGE_ID = 'chargeId';
    private const string DOMAIN_AMOUNT = 'amount';
    private const string DOMAIN_FINAL_AMOUNT = 'finalAmount';
    private const string DOMAIN_TOTAL_KWH_CHARGED = 'totalKwhCharged';
    private const string DOMAIN_API_OCCURRED_ON = 'occurredOn';

    private function __construct(
        private(set) int $chargeId { get => $this->chargeId; },
        private(set) float $amount { get => $this->amount; },
        private(set) float $finalAmount { get => $this->finalAmount; },
        private(set) float $totalKwhCharged { get => $this->totalKwhCharged; },
        private(set) DateTimeValueObject $occurredOn { get => $this->occurredOn; },
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (null === $data[self::API_RECHARGE]) {
            // OJO, SE TRATA DE UNA RESERVA EXPIRADA, POR LO QUE NO HAY CARGA
            $data[self::API_RECHARGE] = [
                self::API_RECHARGE_ID => 0,
                self::API_KWH_CONSUMED => 0,
            ];
        }

        if (null === $data[self::API_RECHARGE][self::API_RECHARGE_ID]) {
            $data[self::API_RECHARGE][self::API_RECHARGE_ID] = (int) $data[self::API_RECHARGE][self::API_SESSION_ID];
        }

        $occurredOn = DateTimeValueObject::fromFormat(
            self::API_DEFAULT_DATE_FORMAT,
            $data[self::API_MOVEMENT_DATE],
        );

        if (false === $occurredOn) {
            throw new RuntimeException('Charge date is not valid');
        }

        return new self(
            $data[self::API_RECHARGE][self::API_RECHARGE_ID],
            $data[self::API_FINAL_PRICE],
            $data[self::API_FINAL_MOVEMENTS_PRICE],
            $data[self::API_RECHARGE][self::API_KWH_CONSUMED],
            $occurredOn,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            self::DOMAIN_CHARGE_ID => $this->chargeId,
            self::DOMAIN_AMOUNT => $this->amount,
            self::DOMAIN_FINAL_AMOUNT => $this->finalAmount,
            self::DOMAIN_TOTAL_KWH_CHARGED => $this->totalKwhCharged,
            self::DOMAIN_API_OCCURRED_ON => $this->occurredOn->value(),
        ];
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}
