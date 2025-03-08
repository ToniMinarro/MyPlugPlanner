<?php

declare(strict_types=1);

namespace IberdrolaApi\ChargePoint\Entrypoint\Requests\ChargePoint;

use Symfony\Component\Validator\Constraints as Assert;
use Shared\Infrastructure\Entrypoint\Requests\BaseRequest;
use IberdrolaApi\ChargePoint\Domain\Model\IberdrolaApiRequestInterface;

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
