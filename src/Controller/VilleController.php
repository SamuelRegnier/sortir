<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\CreationVilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    #[Route('/ville/creer', name: 'ville_creer')]
    public function creer(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('ville_liste');
        }

        $ville = new Ville();
        $formVille = $this->createForm(CreationVilleType::class, $ville);
        $formVille->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()){
            $nom = $formVille->get('nom')->getData();
            $codePostal = $formVille->get('codePostal')->getData();
            $ville->setNom($nom);
            $ville->setCodePostal($codePostal);
            $this->addFlash('success', 'Création de la ville réalisée avec succès!');
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('ville_afficher', ['id'=> $ville->getId() ]);
        }

        return $this->renderForm('ville/creer.html.twig',
            compact('formVille')
        );
    }

    #[Route('/ville/modifier/{id}', name: 'ville_modifier')]
    public function modifier(
        Request $request,
        EntityManagerInterface $entityManager,
        VilleRepository $villeRepository,
        Ville $id
    ): Response
    {
        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('ville_liste');
        }

        $ville = $villeRepository->findOneBy(array('id'=>$id));
        $formVille = $this->createForm(CreationVilleType::class, $ville);
        $formVille->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()){
            //$nom = $formVille->get('nom')->getData();
            //$codePostal = $formVille->get('codePostal')->getData();
            //$ville->setNom($nom);
            //$ville->setCodePostal($codePostal);
            $this->addFlash('success', 'Modification de la ville réalisée avec succès!');
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('ville_afficher', ['id'=> $ville->getId() ]);
        }

        return $this->renderForm('ville/modifier.html.twig',
            compact('formVille')
        );
    }

    #[Route('/ville', name: 'ville_liste')]
    public function liste(
        VilleRepository $villeRepository
    ): Response
    {

        $villes = $villeRepository->findAll();

        return $this->render('ville/liste.html.twig',
            compact('villes')
        );
    }

    #[Route('/ville/afficher/{id}', name: 'ville_afficher')]
    public function afficher(
        VilleRepository $villeRepository,
        Ville $id
    ): Response
    {
        $ville = $villeRepository->findOneBy(array('id'=>$id));

        if (!$this->getUser()->isAdministrateur()) {
            return $this->render('ville/afficher_utilisateur.html.twig',
                compact('ville')
            );
        }
        return $this->render('ville/afficher.html.twig',
            compact('ville')
        );
    }

    #[Route('/ville/supprimer/{id}', name: 'ville_supprimer')]
    public function supprimer(
        VilleRepository $villeRepository,
        EntityManagerInterface $entityManager,
        Ville $id
    ): Response
    {
        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('ville_liste');
        }

        $ville = $villeRepository->findOneBy(array('id'=>$id));
        $entityManager->remove($ville);
        $entityManager->flush();

        return $this->redirectToRoute('ville_liste');
    }
}
