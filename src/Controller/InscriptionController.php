<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Repository\InscriptionRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    #[Route('_inscription/{id}', name:'_inscription_sortie', requirements: ['id'=>'\d+'])]
    public function inscription(
        Sortie $id,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager
    ):response
    {
        $sortie = $sortieRepository->findOneBy(array('id'=>$id));
        $user = $this->getUser();

        $inscription = new Inscription();
        $inscription->setDateInscription(new \dateTime());
        $inscription->setSortie($sortie);
        $inscription->setParticipant($user);

        $entityManager->persist($inscription);
        $entityManager->flush();

        return $this->redirectToRoute('accueil_index', [
            "listeInscrit"=>$inscription,

        ]);
    }

    #[Route('_desinscription/{id}', name:'_desinscription_sortie', requirements: ['id'=>'\d+'])]
    public function desinscription(
        Sortie $id,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
        InscriptionRepository $inscriptionRepository
    ):response
    {
        $sortie = $sortieRepository->findOneBy(array('id'=>$id));
        $user = $this->getUser();

        $inscription = $inscriptionRepository->findOneBy(array('sortie'=>$sortie->getId()));

        $entityManager->remove($inscription);

        $entityManager->flush();

        return $this->redirectToRoute('accueil_index', [
            "listeInscrit"=>$inscription,

        ]);
    }

}
