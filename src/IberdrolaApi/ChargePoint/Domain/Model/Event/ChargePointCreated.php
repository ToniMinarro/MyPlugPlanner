<?php

declare(strict_types=1);

namespace MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Model\Event;

use MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Model\ChargePoint;
use MyPlugPlanner\Shared\Domain\CompanyName;
use MyPlugPlanner\Shared\Domain\ServiceName;
use PcComponentes\Ddd\Domain\Model\DomainEvent;
use PcComponentes\TopicGenerator\Topic;

final class ChargePointCreated extends DomainEvent implements ChargePointDomainEvent
{
    final public const string ID = 'id';
    private const string NAME = 'created';
    private const string VERSION = '1';

    private int $id;

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

    public function id(): int
    {
        return $this->id;
    }

    protected function assertPayload(): void
    {
        $payload = $this->messagePayload();

        $this->id = $payload[self::ID];
    }
}
