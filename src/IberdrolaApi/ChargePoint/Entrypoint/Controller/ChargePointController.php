<?php

declare(strict_types=1);

namespace IberdrolaApi\ChargePoint\Entrypoint\Controller;

use IberdrolaApi\ChargePoint\Application\GetInfo\GetInfoQuery;
use IberdrolaApi\ChargePoint\Entrypoint\Requests\ChargePoint\GetInfoRequest;
use PcComponentes\Ddd\Domain\Model\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/charge-point', name: 'api_charge_point_')]
class ChargePointController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $executeQueryMessage)
    {
        $this->messageBus = $executeQueryMessage;
    }

    #[Route(path: '/{chargePointId}', name: 'get_info', methods: ['GET'])]
    public function getInfo(GetInfoRequest $request): JsonResponse
    {
        $query = GetInfoQuery::fromPayload(Uuid::v4(), $request->getPayload());
        $response = $this->handle($query);

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
