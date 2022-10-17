<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Repository\InscriptionRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accueil', name: 'accueil')]
class AccueilController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(SortieRepository $sortieRepository,
                          SiteRepository $siteRepository,
                          InscriptionRepository $inscriptionRepository,
                          ParticipantRepository $participantRepository,
                          Request $request
    ): Response
    {
        $sorties = $sortieRepository->findAll();
        $sites = $siteRepository->findAll();
        $inscription = $inscriptionRepository->findAll();
        $participant = $participantRepository->findAll();

        $site = $request->query->get('site');
        $nom = $request->query->get('search');
        $dateDebut = $request->query->get('dateDebut');
        $dateFin = $request->query->get('dateFin');
        $organisateur = $request->query->get('sortiesOrga');
        $inscrit = $request->query->get('sortiesInscrit');
        $nonInscrit = $request->query->get('sortiesNonInscrit');
        $sortiesPassees = $request->query->get('sortiesPassees');

        $filtre = $sortieRepository->findByFiltre($organisateur, $site, $inscrit, $nonInscrit, $sortiesPassees, $nom, $dateDebut, $dateFin, $this->getUser());


        return $this->render('accueil/index.html.twig', [
            "sorties"=>$sorties,
            "sites"=>$sites,
            "inscription"=>$inscription,
            "participant"=>$participant,
            "filtre"=>$filtre
        ]);
    }


}
