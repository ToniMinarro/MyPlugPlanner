<?php

declare(strict_types=1);

namespace MyPlugPlanner\IberdrolaApi\ChargePoint\Entrypoint\Controller;

use MyPlugPlanner\IberdrolaApi\ChargePoint\Application\GetInfo\GetInfoQuery;
use MyPlugPlanner\IberdrolaApi\ChargePoint\Entrypoint\Requests\ChargePoint\GetInfoRequest;
use PcComponentes\Ddd\Domain\Model\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

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
