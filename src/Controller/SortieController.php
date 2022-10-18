<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    // Créer une sortie

    #[Route('/creer/sortie', name: 'app_creer_sortie')]
    public function index(
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

            if ($request->query->get('selectionner', true)){
                $sortie->setEtats($etatCreee);
            }
            if ($request->query->get('selectionner', false)){
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

    // Page annulation d'une sortie

    #[Route('/sortie/annulation/{id}', name: 'sortie_annulation', requirements: ['id'=>'\d+'])]
    public function SortieAnnulation(
        Sortie $id,
    ): Response
    {
        return $this->render('sortie/annulation.html.twig', [
            "sortie" => $id,
        ]);
    }

    // Annuler une sortie

    #[Route('/sortie/annulee/{id}', name: 'sortie_annulee', requirements: ['id'=>'\d+'])]
    public function SortieAnnulee(
        Sortie $id,
        SortieRepository $SortieRepository,
        EtatRepository $etatRepository,
        InscriptionRepository $inscriptionRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = $this->getUser();
        $sortie = $SortieRepository->find($id);
        $etat = $etatRepository->findOneBy(array('id'=> 6));

        if ($sortie->getOrganisateur() === $user) {
            $sortie->setEtats($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
        }
        return $this->redirectToRoute('accueil_index');
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

    // Filtres


}
