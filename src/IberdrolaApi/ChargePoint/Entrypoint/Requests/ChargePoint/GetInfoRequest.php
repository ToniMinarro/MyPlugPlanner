<?php

declare(strict_types=1);

namespace IberdrolaApi\ChargePoint\Entrypoint\Requests\ChargePoint;

use IberdrolaApi\ChargePoint\Domain\Model\IberdrolaApiRequestInterface;
use Shared\Infrastructure\Entrypoint\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetInfoRequest extends BaseRequest implements IberdrolaApiRequestInterface
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
