<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use PcComponentes\TopicGenerator\Company;

class CompanyName extends Company
{
    private const string NAME = 'toniminarro';

    public function name(): string
    {
        return self::NAME;
    }
}
