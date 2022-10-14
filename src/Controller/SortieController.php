<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/creer/sortie', name: 'app_creer_sortie')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository,
        LieuRepository  $lieuRepository,
        SiteRepository  $siteRepository
    ): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $sortie = new Sortie();
        $etat = $etatRepository->findOneBy(array('id'=> 1));
        $site = $siteRepository->findOneBy(array('id'=>$user->getSite()));

        $sortie->setOrganisateur($user);
        $sortie->setEtats($etat);
        $sortie->setSite($site);
            if ($user->isAdministrateur()) {
                $sortie->setNombreParticipants(0);
            }
            if (!$user->isAdministrateur()) {
                $sortie->setNombreParticipants(1);
            }
        $lieu = $lieuRepository->findOneBy(array('id'=>$sortie->getLieux()));

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function detail(Sortie $id): Response
    {
        return $this->render('sortie/detail.html.twig', [
            "sortie" => $id
        ]);
    }


    // Sorties dont je suis l'organisateur

    #[Route('/sortie/organisateur', name: 'sortie_organisateur')]
    public function SortieOrganisteur(
        SortieRepository $SortieRepository,
    ): Response
    {
        $user = $this->getUser();
        $sorties = $SortieRepository->findBy([
            'organisateur' => $user->getId()
        ]);
        return $this->render('sortie/organisateur.html.twig', [
            'sorties' => $sorties
        ]);
    }

    // Sorties passees

    #[Route('/sortie/passee', name: 'sortie_passee')]
    public function SortiePassee(
        SortieRepository $SortieRepository,
    ): Response
    {
        $sortiePassees = $SortieRepository->findBy([
            'etats' => '5'
        ]);
        return $this->render('sortie/passee.html.twig', [
            'sortiePassees' => $sortiePassees
        ]);
    }

}
