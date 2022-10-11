<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accueil', name: 'accueil')]
class AccueilController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('accueil/index.html.twig', [
            "sorties"=>$sorties
        ]);
    }

    #[Route('/sortie/detail/{id}', name:'sortie_detail', requirements: ['id'=>'\d+'])]
    public function detail(Sortie $id):response
    {
        return $this->render('sortie/detail', [
            "sortie" => $id
        ]);
    }
}
