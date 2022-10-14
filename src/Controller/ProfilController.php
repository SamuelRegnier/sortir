<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CreationProfilType;
use App\Form\MotDePasseType;
use App\Form\ParticipantType;
use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('accueil/profil/creation', name: 'profil_creerProfil')]
    public function creerProfil(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        Request $request,
    ): Response
    {
        $profil = new Participant();
        $formProfil = $this->createForm(CreationProfilType::class,$profil);

        $formProfil->handleRequest($request);

        if ($formProfil->isSubmitted() && $formProfil->isValid()) {
            if($formProfil->get('password')->getData() == $formProfil->get('confirmationPassword')->getData()) {
                $profil->setPassword(
                    $passwordHasher->hashPassword(
                        $profil,
                        $formProfil->get('password')->getData()
                    )
                );
                $profil->setRoles([]);
                $profil->setActif(true);
                $this->addFlash('success', 'Création du profil réalisé avec succès!');
                $em->persist($profil);
                $em->flush();
                return $this->redirectToRoute('accueil_index');
            }
            $this->addFlash('danger', 'Erreur lors de la confirmation du mot de passe!');
            return $this->redirectToRoute('profil_creerProfil');
        }

        return $this->renderForm('profil/creation.html.twig',
            compact('formProfil')
        );
    }

    #[Route('accueil/profil/affichage', name: 'profil_affichageProfil')]
    public function affichageProfil(): Response
    {
        $profil = $this->getUser();

        return $this->render('profil/affichage.html.twig',
            compact('profil'));
    }

    #[Route('accueil/profil/modifier', name: 'profil_modifierProfil')]
    public function modifierProfil(
        EntityManagerInterface $em,
        Request $request,
    ): Response
    {
        $profil = $this->getUser();
        $formProfil = $this->createForm(ParticipantType::class,$profil);

        $formProfil->handleRequest($request);

        if ($formProfil->isSubmitted() && $formProfil->isValid()) {
            $this->addFlash('success', 'Modification(s) réalisée(s) avec succès!');
            $em->persist($profil);
            $em->flush();
            return $this->redirectToRoute('profil_affichageProfil');
        }

        return $this->renderForm('profil/modifier.html.twig',
            compact('formProfil')
        );
    }

    #[Route('accueil/profil/modifier/mdp', name: 'profil_modifierMdp')]
    public function modifierMdp(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        Request $request,
    ): Response
    {
        $ancienProfil = $this->getUser();
        $profil = $this->getUser();
        $formMdp = $this->createForm(MotDePasseType::class,$profil);

        $formMdp->handleRequest($request);

        if ($formMdp->isSubmitted() && $formMdp->isValid()) {
            $hashMdpActuel = $passwordHasher->isPasswordValid(
                $ancienProfil,
                $formMdp->get('Password')->getData()
            );
            if ($hashMdpActuel && $formMdp->get('NouveauPassword')->getData() == $formMdp->get('ConfirmationPassword')->getData()) {
                $profil->setPassword(
                    $passwordHasher->hashPassword(
                    $profil,
                    $formMdp->get('NouveauPassword')->getData()
                    )
                );
                $em->persist($profil);
                $em->flush();
                $this->addFlash('success', 'Mot de passe modifié avec succès!');
                return $this->redirectToRoute('profil_modifierProfil');
            }

            $this->addFlash('danger', 'Mot de passe actuel non valide ou erreur de confirmation du nouveau Mot De Passe!');
            return $this->redirectToRoute('profil_modifierMdp');
        }

        return $this->renderForm('profil/motDePasse.html.twig',
            compact('formMdp'));
    }

    #[Route('accueil/profil/affichage/{id}', name: 'profil_affichageProfilInscrit', requirements: ['id' => '\d+'])]
    public function affichageProfilInscrit(Participant $idParticipant, SortieRepository $sortieRepository, InscriptionRepository $inscriptionRepository): Response
    {
        return $this->render('profil/affichage_profil_inscrit.html.twig', [
            "affichageProfilInscrit" => $idParticipant,
        ]);

    }
}
