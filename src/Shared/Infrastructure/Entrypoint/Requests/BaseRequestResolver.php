<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Entrypoint\Requests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class BaseRequestResolver implements ValueResolverInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function supports(ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), BaseRequest::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $className = $argument->getType();

        if (!class_exists($className)) {
            throw new BadRequestException("Request class {$className} does not exist.");
        }

        yield new $className($request, $this->validator);
    }
}
