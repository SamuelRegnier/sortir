<?php

namespace App\Controller;

use App\Form\MotDePasseType;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
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
        UserPasswordHasherInterface $hasher,
        Request $request,
    ): Response
    {
        $profil = $this->getUser();
        $mdp = $profil->getPassword();
        $formMdp = $this->createForm(MotDePasseType::class,$profil);

        $formMdp->handleRequest($request);

        if ($formMdp->isSubmitted() && $formMdp->isValid()) {
            $mdpActuel = $formMdp->get('Password');
            $hashMdpActuel = $hasher->encodePassword($profil, $mdpActuel);
            if ($hashMdpActuel == $mdp && $formMdp->get('NouveauPassword') == $formMdp->get('ConfirmationPassword')) {
                $nouveauMdp = $formMdp->get('NouveauPassword');
                $hashNouveauMdp = $hasher->encodePassword($profil, $nouveauMdp);
                $profil->setPassword($hashNouveauMdp);
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
}
