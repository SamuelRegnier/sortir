<?php

namespace App\Controller;

use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('accueil/profil', name: 'profil_affichageProfil')]
    public function affichageProfil(
        EntityManagerInterface $em,
        Request $request,
    ): Response
    {
        $profil = $this->getUser();
        $formProfil = $this->createForm(ParticipantType::class,$profil);

        $formProfil->handleRequest($request);

        if ($formProfil->isSubmitted() && $formProfil->isValid()) {
            $em->persist($profil);
            $em->flush();
            return $this->redirectToRoute('accueil_index');
        }

        return $this->renderForm('profil/index.html.twig',
            compact('formProfil'));
    }
}
