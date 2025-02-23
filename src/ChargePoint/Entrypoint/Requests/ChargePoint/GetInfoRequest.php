<?php

declare(strict_types=1);

namespace App\ChargePoint\Entrypoint\Requests\ChargePoint;

use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Infrastructure\Entrypoint\Requests\BaseRequest;

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
