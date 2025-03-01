<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Entrypoint\Requests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract readonly class BaseRequest implements \JsonSerializable
{
    public function __construct(
        private Request $request,
        private ValidatorInterface $validator,
    ) {
        $this->populate();
        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    private function populate(): void
    {
        $payload = array_merge(
            $this->request->attributes->get('_route_params', []),
            $this->request->query->all(),
            $this->request->request->all()
        );

        foreach ($payload as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
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
            'errors' => array_map(fn($error) => [
                'property' => $error->getPropertyPath(),
                'value' => $error->getInvalidValue(),
                'message' => $error->getMessage(),
            ], iterator_to_array($errors)),
        ];

        new JsonResponse($messages, Response::HTTP_BAD_REQUEST)->send();
    }

    public function getPayload(): array
    {
        return $this->jsonSerialize();
    }

    function autoValidateRequest(): bool
    {
        return true;
    }
}
