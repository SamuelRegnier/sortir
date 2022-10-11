<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accueil', name: 'accueil')]
class AccueilController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(SortieRepository $sortieRepository,
                          SiteRepository $siteRepository
    ): Response
    {
//        dd($sortieRepository->findAll());
//        $sorties = $sortieRepository->findAll();
        $sites = $siteRepository->findAll();
        return $this->render('accueil/index.html.twig', [
//            "sorties"=>$sorties,
            "sites"=>$sites
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
