<?php

declare(strict_types=1);

namespace MyPlugPlanner\App\ChargePoint\Entrypoint\Controller;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use MyPlugPlanner\IberdrolaApi\ChargePoint\Domain\Model\ChargePoint;

#[Route(path: '/charge-point', name: 'charge_point_')]
class ChargePointController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $executeQueryMessage)
    {
        $this->messageBus = $executeQueryMessage;
    }

    #[Route(path: '/{chargePointId}', name: 'get_info', methods: ['GET'])]
    public function getInfoView(int $chargePointId): Response
    {
        return $this->render(
            'app/charge_point/get_info.html.twig',
            [
                'chargePointId' => $chargePointId,
            ],
        );
    }
}
