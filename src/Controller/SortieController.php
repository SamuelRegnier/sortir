<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulationSortieType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SortieController extends AbstractController
{
    // Créer une sortie

    #[Route('/creer/sortie', name: 'sortie_creer')]
    public function creer(
        HttpClientInterface $client,
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository,
        LieuRepository  $lieuRepository,
        SiteRepository  $siteRepository,
    ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $sortie = new Sortie();
        $etatCreee = $etatRepository->findOneBy(array('id'=> 1));
        $etatOuvert = $etatRepository->findOneBy(array('id'=> 2));
        $site = $siteRepository->findOneBy(array('id'=>$user->getSite()));

        $sortie->setOrganisateur($user);
        $sortie->setSite($site);

        if ($user->isAdministrateur()) {
            $sortie->setNombreParticipants(0);
        }
        if (!$user->isAdministrateur()) {
            $inscription = new Inscription();
            $inscription -> setSortie($sortie);
            $inscription->setParticipant($user);
            $inscription->setDateInscription(new \dateTime());
            $sortie->setNombreParticipants(1);
        }
        $lieu = $lieuRepository->findOneBy(array('id'=>$sortie->getLieux()));
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->get("sortie")["selectionner"]){
                $sortie->setEtats($etatCreee);
            } else {
                $sortie->setEtats($etatOuvert);
            }
            if (!$user->isAdministrateur()) {
                $entityManager->persist($inscription);
            }

            $detailLieu = $request->get("sortie")["lieux"];
            $lieu = $lieuRepository->find($detailLieu);
//            dd($lieu);
            $nomLieu = $lieu->getNom();
//            dd($nomLieu);
            $rueLieu = $lieu->getRue();
//            dd($rueLieu);

            $this->client = $client;
            $response = $this->client->request(
                'GET',
                'http://nominatim.openstreetmap.org/search?format=json&limit=1&q='.$rueLieu.' '.$nomLieu
            );

            $content = $response->toArray();

//            $json=file_get_contents('http://nominatim.openstreetmap.org/search?format=json&limit=1&q='.$rueLieu.' '.$nomLieu);
//            $obj = json_decode($json, true);
            $latitude = $content[0]['lat'];
//            dd($latitude);
            $longitude = $content[0]['lon'];
            $lieu->setLatitude($latitude);
            $lieu->setlongitude($longitude);

            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('accueil_index');
        }
        return $this->render('creer_sortie/index.html.twig',[
            'form'=>$form->createView(),
            'lieu'=>$lieu
        ]);
    }

    #[Route('/modifier/sortie', name: 'sortie_modifier')]
    public function modifier(
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository,
        LieuRepository  $lieuRepository,
        SiteRepository  $siteRepository,
    ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $sortie = new Sortie();
        $etatCreee = $etatRepository->findOneBy(array('id'=> 1));
        $etatOuvert = $etatRepository->findOneBy(array('id'=> 2));
        $site = $siteRepository->findOneBy(array('id'=>$user->getSite()));

        $sortie->setOrganisateur($user);
        $sortie->setSite($site);

        if ($user->isAdministrateur()) {
            $sortie->setNombreParticipants(0);
        }
        if (!$user->isAdministrateur()) {
            $inscription = new Inscription();
            $inscription -> setSortie($sortie);
            $inscription->setParticipant($user);
            $inscription->setDateInscription(new \dateTime());
            $sortie->setNombreParticipants(1);
        }
        $lieu = $lieuRepository->findOneBy(array('id'=>$sortie->getLieux()));
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->get("sortie")["selectionner"]){
                $sortie->setEtats($etatCreee);
            } else {
                $sortie->setEtats($etatOuvert);
            }
            if (!$user->isAdministrateur()) {
                $entityManager->persist($inscription);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('accueil_index');
        }
        return $this->render('creer_sortie/index.html.twig',[
            'form'=>$form->createView(),
            'lieu'=>$lieu
        ]);
    }

    #[Route('/sortie/detail/{id}', name: 'sortie_detail', requirements: ['id' => '\d+'])]
    public function detail(Sortie $id,
                            ParticipantRepository $participantRepository,
                            InscriptionRepository $inscriptionRepository
    ): Response
    {
        $Participant = $participantRepository->findAll();
        $inscription = $inscriptionRepository->findAll();
        return $this->render('sortie/detail.html.twig', [
            "sortie" => $id,
            "participant" => $Participant,
            "inscription" => $inscription
        ]);
    }

    #[Route('/sortie/annulee/{id}', name: 'sortie_annulee', requirements: ['id'=>'\d+'])]
    public function SortieAnnulee(
        Sortie $id,
        SortieRepository $SortieRepository,
        EtatRepository $etatRepository,
        InscriptionRepository $inscriptionRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $user = $this->getUser();
        $sortie = $SortieRepository->find($id);
        $etat = $etatRepository->findOneBy(array('id'=> 6));

        $formMotifAnnulation = $this->createForm(AnnulationSortieType::class,$sortie);
        $formMotifAnnulation->handleRequest($request);

        if ($sortie->getOrganisateur() === $user)
            if ($formMotifAnnulation->isSubmitted() && $formMotifAnnulation->isValid()) {
                $sortie->setEtats($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
                return $this->redirectToRoute('accueil_index');
            }
        return $this->renderForm('sortie/annulation.html.twig',
            compact('formMotifAnnulation', 'sortie')
        );
    }

    // Afficher sortie annulée

    #[Route('/sortie/annulee/detail/{id}', name: 'sortie_annulee_Detail', requirements: ['id'=>'\d+'])]
    public function SortieAnnuleeDetail(
        Sortie $id,
    ): Response
    {
        return $this->render('sortie/annulee_detail.html.twig', [
            "sortie" => $id,
        ]);
    }
}
