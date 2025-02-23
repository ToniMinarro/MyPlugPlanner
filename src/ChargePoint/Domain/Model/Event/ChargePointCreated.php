<?php

declare(strict_types=1);

namespace App\ChargePoint\Domain\Model\Event;

use App\Shared\Domain\CompanyName;
use App\Shared\Domain\ServiceName;
use PcComponentes\TopicGenerator\Topic;
use App\ChargePoint\Domain\Model\ChargePoint;
use PcComponentes\Ddd\Domain\Model\DomainEvent;

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
