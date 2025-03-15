<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Application\List;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use IberdrolaApi\ChargePoint\Domain\Service\ChargePointService;
use IberdrolaApi\Charge\Domain\Service\ListChargesResponseTransformer;

#[AsMessageHandler]
readonly class ListChargesHandler
{
    public function __construct(
        private ChargePointService $chargePointService,
        private ListChargesResponseTransformer $responseTransformer,
    ) {
    }

    public function __invoke(ListChargesQuery $query): ListChargesResponse
    {
        $apiChargesData = $this->chargePointService->listCharges();
        $charges = ($this->responseTransformer)($apiChargesData);

        return ListChargesResponse::createFromApiCharges($charges);
    }
}
