<?php

declare(strict_types=1);

namespace Shared\Domain\Model;

use JsonSerializable;
use PcComponentes\Ddd\Domain\Model\DomainEvent;

abstract class DomainModel implements JsonSerializable
{
    private array $events = [];

    protected function __construct()
    {
    }

    abstract public static function modelName(): string;

    final public function events(): array
    {
        return $this->events;
    }

    final protected function recordThat(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
