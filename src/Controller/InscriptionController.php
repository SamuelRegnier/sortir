<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
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
        InscriptionRepository $inscriptionRepository,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository
    ):response
    {
        $sortie = $sortieRepository->findOneBy(array('id'=>$id));
        $user = $this->getUser();
        $dejaInscrit = $inscriptionRepository->findOneBy(['participant'=>$user, 'sortie'=>$sortie]);
        $etat = $etatRepository->findOneBy(array('id'=> 3));

        if(isset($dejaInscrit)){
            return $this->redirectToRoute('accueil_index');
        }
        if($sortie->getOrganisateur() !== $user && $sortie->getEtats()->getId() == 2) {
            $inscription = new Inscription();
            $inscription->setDateInscription(new \dateTime());
            $inscription->setSortie($sortie);
            $inscription->setParticipant($user);
            $nbParticipants = $sortie->getNombreParticipants();
            $sortie->setNombreParticipants($nbParticipants + 1);

            if($sortie->getNombreParticipants() == $sortie->getNbInscriptionsMax()){
                $sortie->setEtats($etat);
            }

            $entityManager->persist($inscription);
            $entityManager->flush();
        }
        return $this->redirectToRoute('accueil_index', [
        ]);
    }

    #[Route('_desinscription/{id}', name:'_desinscription_sortie', requirements: ['id'=>'\d+'])]
    public function desinscription(
        Sortie $id,
        SortieRepository $sortieRepository,
        EntityManagerInterface $entityManager,
        InscriptionRepository $inscriptionRepository,
        EtatRepository $etatRepository
    ):response
    {
        $sortie = $sortieRepository->findOneBy(array('id'=>$id));
        $nbParticipants = $sortie->getNombreParticipants();
        $user = $this->getUser();
        $inscription = $inscriptionRepository->findOneBy(['participant'=>$user, 'sortie'=>$sortie]);
        $etat = $etatRepository->findOneBy(array('id'=> 2));

        if ($sortie->getOrganisateur() === $user) {
            return $this->redirectToRoute('accueil_index');
        }
        if(is_null($inscription)){
            return $this->redirectToRoute('accueil_index');
        }
        if($sortie->getNombreParticipants() == $sortie->getNbInscriptionsMax()){
            $sortie->setEtats($etat);
        }

        $sortie->setNombreParticipants($nbParticipants - 1);
        $entityManager->remove($inscription);
        $entityManager->flush();
        return $this->redirectToRoute('accueil_index');
    }

}
