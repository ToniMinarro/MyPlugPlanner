<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Domain\Model;

use Override;
use Assert\Assertion;
use PcComponentes\Ddd\Domain\Model\ValueObject\CollectionValueObject;
use RuntimeException;

final class Charges extends CollectionValueObject
{
    #[Override]
    public function current(): ?Charge
    {
        $charge = parent::current();

        if (false === $charge) {
            return null;
        }

        if (false === $charge instanceof Charge) {
            throw new RuntimeException('Invalid type');
        }

        return $charge;
    }

    public function add(Charge $charge): self
    {
        return $this->addItem($charge);
    }

    public function remove(Charge $charge): self
    {
        return parent::removeItem($charge);
    }

    #[Override]
    public static function from(array $items): static
    {
        Assertion::allIsInstanceOf($items, Charge::class);

        return parent::from($items);
    }

    public static function empty(): static
    {
        return parent::from([]);
    }

    public static function new(): static
    {
        return self::empty();
    }

    public function totalKwhCharged(): float
    {
        return $this->reduce(
            static fn (float $totalKwhCharged, Charge $charge): float => $totalKwhCharged + $charge->totalKwhCharged,
            0.0,
        );
    }

    public function totalAmount(): float
    {
        return $this->reduce(
            static fn (float $totalAmount, Charge $charge): float => $totalAmount + $charge->finalAmount,
            0.0,
        );
    }
}
