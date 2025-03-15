<?php

declare(strict_types=1);

namespace IberdrolaApi\Charge\Entrypoint\Controller;

use IberdrolaApi\Charge\Application\List\ListChargesQuery;
use IberdrolaApi\Shared\Infrastructure\Service\OAuthTokenManager;
use PcComponentes\Ddd\Domain\Model\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/iberdrola-api/v1/charge', name: 'api_charge_')]
class ChargeController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $executeQueryMessage,
        private readonly OAuthTokenManager $tokenManager,
    ) {
        $this->messageBus = $executeQueryMessage;
    }

    #[Route(path: '/list', name: 'list_charges', methods: ['GET'])]
    public function listCharges(): Response
    {
        $accessToken = $this->tokenManager->getAccessToken();

        if (!$accessToken) {
            return new Response(
                'No se pudo obtener un access token. Requiere autenticación.',
                Response::HTTP_UNAUTHORIZED
            );
        }

        $query = ListChargesQuery::fromPayload(Uuid::v4(), []);
        $response = $this->handle($query);
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
