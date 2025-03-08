<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Entrypoint\Requests;

use JsonSerializable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function is_array;

abstract class BaseRequest implements JsonSerializable
{
    public function __construct(
        private readonly Request $request,
        private readonly ValidatorInterface $validator,
    ) {
        $this->populate();
        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    public function getPayload(): array
    {
        $serializedRequest = $this->jsonSerialize();

        return is_array($serializedRequest)
            ? $serializedRequest
            : [];
    }

    public function autoValidateRequest(): bool
    {
        return true;
    }

    private function populate(): void
    {
        $queryAttributes = $this->request->attributes->get('_route_params');
        $queryAttributes = is_array($queryAttributes) ? $queryAttributes : [];

        $payload = array_merge(
            $queryAttributes,
            $this->request->query->all(),
            $this->request->request->all()
        );

        foreach ($payload as $property => $value) {
            if (property_exists($this, (string) $property)) {
                $this->{(string) $property} = $value;
            }
        }
    }

    private function validate(): void
    {
        $errors = $this->validator->validate($this);
        if (0 === $errors->count()) {
            return;
        }

        $messages = [
            'message' => 'validation_failed',
            'errors' => array_map(static fn ($error): array => [
                'property' => $error->getPropertyPath(),
                'value' => $error->getInvalidValue(),
                'message' => $error->getMessage(),
            ], iterator_to_array($errors)),
        ];

        new JsonResponse($messages, Response::HTTP_BAD_REQUEST)->send();
    }
}
