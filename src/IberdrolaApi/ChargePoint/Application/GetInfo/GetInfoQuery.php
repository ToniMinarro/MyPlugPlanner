<?php

declare(strict_types=1);

namespace MyPlugPlanner\IberdrolaApi\ChargePoint\Application\GetInfo;

use MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Model\ChargePoint;
use MyPlugPlanner\Shared\Domain\CompanyName;
use MyPlugPlanner\Shared\Domain\ServiceName;
use PcComponentes\Ddd\Application\Query;
use PcComponentes\TopicGenerator\Topic;

final class GetInfoQuery extends Query
{
    public const string CHARGE_POINT_ID = 'chargePointId';
    private const string VERSION = '1';
    private const string NAME = 'get_info';

    private int $chargePointId;

    public function chargePointId(): int
    {
        return $this->chargePointId;
    }

    public static function messageName(): string
    {
        return Topic::generate(
            CompanyName::instance(),
            ServiceName::instance(),
            self::VERSION,
            self::messageType(),
            ChargePoint::modelName(),
            self::NAME,
        );
    }

    public static function messageVersion(): string
    {
        return self::VERSION;
    }

    protected function assertPayload(): void
    {
        $this->chargePointId = (int) $this->messagePayload()[self::CHARGE_POINT_ID];
    }
}
