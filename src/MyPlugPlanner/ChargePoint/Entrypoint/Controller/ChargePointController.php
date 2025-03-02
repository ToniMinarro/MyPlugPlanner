<?php

declare(strict_types=1);

namespace MyPlugPlanner\ChargePoint\Entrypoint\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/charge-point', name: 'charge_point_')]
class ChargePointController extends AbstractController
{
    #[Route(path: '/{chargePointId}', name: 'get_info', methods: ['GET'])]
    public function getInfoView(Request $request): Response
    {
        return $this->render(
            'my_plug_planner/charge_point/get_info.html.twig',
            [
                'chargePointId' => $request->get('chargePointId'),
            ],
        );
    }
}
