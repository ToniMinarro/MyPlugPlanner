<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use PcComponentes\TopicGenerator\Service;

class ServiceName extends Service
{
    private const string NAME = 'my-plug-planner';

    public function name(): string
    {
        return self::NAME;
    }
}
