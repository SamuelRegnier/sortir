<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VitrineController extends AbstractController
{
    #[Route('/', name: 'vitrine_vitrine')]
    public function vitrine(): Response
    {
        return $this->render('vitrine/vitrine.html.twig', [
            'controller_name' => 'VitrineController',
        ]);
    }
}
