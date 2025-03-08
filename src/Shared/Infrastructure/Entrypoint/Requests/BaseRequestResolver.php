<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Entrypoint\Requests;

use function sprintf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use IberdrolaApi\ChargePoint\Domain\Model\IberdrolaApiRequestInterface;

final readonly class BaseRequestResolver implements ValueResolverInterface
{
    private const array ENABLED_FOR_CONTEXTS = [
        IberdrolaApiRequestInterface::class,
    ];

    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $classImplements = (array) class_implements(
            (string) $argument->getType()
        );

        $requestInterfaceEnabledInfo = array_intersect(
            $classImplements,
            self::ENABLED_FOR_CONTEXTS,
        );

        if (empty($requestInterfaceEnabledInfo)) {
            return [];
        }

        $className = (string) $argument->getType();
        if (!class_exists($className)) {
            throw new BadRequestException(sprintf('Request class %s does not exist.', $className));
        }

        yield new $className($request, $this->validator);
    }
}
