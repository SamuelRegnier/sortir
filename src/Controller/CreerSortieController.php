<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreerSortieController extends AbstractController
{
    #[Route('/creer/sortie', name: 'app_creer_sortie')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository,
        LieuRepository  $lieuRepository
    ): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $this->getUser();
        $sortie = new Sortie();
        $etat = $etatRepository->findOneBy(array('id'=> 1));

        $sortie->setOrganisateur($this->getUser());
        $sortie->setEtats($etat);

        $lieu = $lieuRepository->findOneBy(array('id'=>$sortie->getLieux()));


        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->render('accueil/index.html.twig', [
                'controller_name' => 'CreerSortieController',
            ]);
        }
        return $this->render('creer_sortie/index.html.twig',[
            'form'=>$form->createView(),
            'lieu'=>$lieu
    ]);
    }
}
