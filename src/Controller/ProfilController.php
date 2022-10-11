<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil/{id}', name: 'profil_affichageProfil')]
    public function affichageProfil(
        ParticipantRepository $participantRepository,
        Request $request,
        $id
    ): Response
    {
        $profil = new Participant();
        $formProfil = $this->createForm(ParticipantType::class,$profil);

        $formProfil->handleRequest($request);

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }
}
