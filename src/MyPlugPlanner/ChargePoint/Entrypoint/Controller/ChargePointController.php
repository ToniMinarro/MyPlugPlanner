<?php

declare(strict_types=1);

namespace MyPlugPlanner\ChargePoint\Entrypoint\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/charge-point', name: 'charge_point_')]
class ChargePointController extends AbstractController
{
    #[Route(path: '/{chargePointId}', name: 'get_info', methods: ['GET'])]
    public function getInfoView(Request $request): Response
    {
        return $this->render(
            'app/charge_point/get_info.html.twig',
            [
                'chargePointId' => $request->get('chargePointId'),
            ],
        );
    }
}
