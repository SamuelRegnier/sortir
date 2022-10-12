<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'app_lieu_ajout')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        LieuRepository $lieuRepository,
        VilleRepository $villeRepository
    ): Response
    {
        $lieu = new Lieu();
        $ville = new Ville();

        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $ville->setNom($form->get('ville')->getData());
        $ville->setCodePostal($form->get('code_postal')->getData());

        $lieu->setVilles($ville);

            $entityManager->persist($ville);
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('app_creer_sortie');
        }

        return $this->render('lieu/index.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
}
