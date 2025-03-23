<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Entrypoint\Controller;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use PcComponentes\Ddd\Domain\Model\ValueObject\Uuid;
use IberdrolaApi\Charge\Application\List\ListChargesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/iberdrola-api/v1/charge', name: 'api_charge_')]
class ChargeController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $executeQueryMessage,
    ) {
        $this->messageBus = $executeQueryMessage;
    }

    #[Route(path: '/list', name: 'list_charges', methods: ['GET'])]
    public function listCharges(): Response
    {
        $query = ListChargesQuery::fromPayload(Uuid::v4(), []);
        $response = $this->handle($query);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
