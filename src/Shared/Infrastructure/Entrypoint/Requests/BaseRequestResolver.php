<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Entrypoint\Requests;

use IberdrolaApi\ChargePoint\Domain\Model\IberdrolaApiRequestInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class BaseRequestResolver implements ValueResolverInterface
{
    private const array ENABLED_FOR_CONTEXTS = [
        IberdrolaApiRequestInterface::class,
    ];

    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (empty(array_intersect(class_implements($argument->getType()), self::ENABLED_FOR_CONTEXTS))) {
            return [];
        }

        $className = $argument->getType();
        if (!class_exists($className)) {
            throw new BadRequestException("Request class {$className} does not exist.");
        }

        yield new $className($request, $this->validator);
    }
}
