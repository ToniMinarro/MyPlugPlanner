<?php

declare(strict_types=1);

namespace MyPlugPlanner\IberdrolaApi\ChargePoint\Entrypoint\Requests\ChargePoint;

use MyPlugPlanner\Shared\Infrastructure\Entrypoint\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class GetInfoRequest extends BaseRequest
{
    #[Assert\NotBlank, Assert\Type('numeric')]
    protected(set) mixed $chargePointId;

    public function jsonSerialize(): array
    {
        return [
            'chargePointId' => $this->chargePointId,
        ];
    }
}
