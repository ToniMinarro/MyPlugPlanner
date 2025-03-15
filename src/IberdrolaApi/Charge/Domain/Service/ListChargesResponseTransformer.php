<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Domain\Service;

use IberdrolaApi\Charge\Application\List\Dto\ApiChargeDto;
use IberdrolaApi\Charge\Domain\Model\Charge;
use IberdrolaApi\Charge\Domain\Model\Charges;
use RuntimeException;
use function array_key_exists;
use function is_array;

final class ListChargesResponseTransformer
{
    private const string DATA_SET = 'recordList';
    public function __invoke(array $apiListChargesDataCollection): Charges
    {
        if (
            false === array_key_exists('recordList', $apiListChargesDataCollection)
            || false === is_array($apiListChargesDataCollection['recordList'])
            || [] === $apiListChargesDataCollection['recordList']
        ) {
            return Charges::empty();
        }

        $domainCharges = Charges::new();
        foreach ($apiListChargesDataCollection[self::DATA_SET] as $apiListChargesData) {
            if (false === is_array($apiListChargesData)) {
                throw new RuntimeException('Invalid data');
            }

            $domainCharge = ApiChargeDto::fromArray($apiListChargesData);
            $domainCharges = $domainCharges->add(
                Charge::from(
                    $domainCharge->toArray(),
                ),
            );
        }

        return $domainCharges->sort(
            static fn (Charge $chargeA, Charge $chargeB): int => $chargeA->occurredOn <=> $chargeB->occurredOn,
        );
    }
}
