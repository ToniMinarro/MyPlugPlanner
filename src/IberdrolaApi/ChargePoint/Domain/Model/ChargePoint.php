<?php

declare(strict_types=1);

namespace MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Model;

use MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Model\Event\ChargePointCreated;
use MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Model\ValueObject\Id;
use MyPlugPlanner\Shared\Domain\Model\DomainModel;
use PcComponentes\Ddd\Domain\Model\ValueObject\DateTimeValueObject;
use PcComponentes\Ddd\Domain\Model\ValueObject\Uuid;
use PcComponentes\Ddd\Util\Message\ValueObject\AggregateId;

final class ChargePoint extends DomainModel
{
    private const string ID = 'id';
    private const string NAME = 'charge-point';

    protected function __construct(
        private(set) readonly Id $id,
    )
    {
        parent::__construct();
    }

    public static function create(Id $id): self
    {
        $chargePoint = new self($id);

        $chargePoint->recordThat(
            ChargePointCreated::fromPayload(
                Uuid::v4(),
                AggregateId::from((string) $chargePoint->id()->value()),
                DateTimeValueObject::now(),
                [
                    ChargePointCreated::ID => $chargePoint->id(),
                ]
            )
        );

        return $chargePoint;
    }

    public function from(array $chargePointData): self
    {
        $id = $chargePointData[self::ID];

        return new self($id);
    }

    public function id(): Id
    {
        return $this->id;
    }

    public static function modelName(): string
    {
        return self::NAME;
    }

    public function jsonSerialize(): array
    {
        return [
            self::ID => $this->id
        ];
    }
}
