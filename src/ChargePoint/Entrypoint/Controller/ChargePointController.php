<?php

declare(strict_types=1);

namespace App\ChargePoint\Entrypoint\Controller;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use PcComponentes\Ddd\Domain\Model\ValueObject\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;
use App\ChargePoint\Application\GetInfo\GetInfoQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\ChargePoint\Entrypoint\Requests\ChargePoint\GetInfoRequest;

class ChargePointController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $executeQueryMessage)
    {
        $this->messageBus = $executeQueryMessage;
    }

    public function getInfo(GetInfoRequest $request): JsonResponse
    {
        $query = GetInfoQuery::fromPayload(Uuid::v4(), $request->getPayload());
        $response = $this->handle($query);

        return new JsonResponse($response,Response::HTTP_OK);
    }
}
