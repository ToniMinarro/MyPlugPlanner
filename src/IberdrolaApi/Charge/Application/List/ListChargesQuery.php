<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Application\List;

use IberdrolaApi\Charge\Domain\Model\Charge;
use PcComponentes\Ddd\Application\Query;
use PcComponentes\TopicGenerator\Topic;
use Shared\Domain\CompanyName;
use Shared\Domain\ServiceName;

final class ListChargesQuery extends Query
{
    private const string VERSION = '1';
    private const string NAME = 'list_charge';

    public static function messageName(): string
    {
        return Topic::generate(
            CompanyName::instance(),
            ServiceName::instance(),
            self::VERSION,
            self::messageType(),
            Charge::modelName(),
            self::NAME,
        );
    }

    public static function messageVersion(): string
    {
        return self::VERSION;
    }

    protected function assertPayload(): void
    {
    }
}
