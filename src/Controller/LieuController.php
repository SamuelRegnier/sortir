<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LieuController extends AbstractController
{
    #[Route('/lieu/ajouter', name: 'lieu_ajouter')]
    public function ajouter(
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $client,
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('lieu_liste');
        }

        $lieu = new Lieu();

        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Méthode pour transformer l'adresse en Latitdue et Longitude
            $nomLieu = $lieu->getVilles()->getNom();
            $cpLieu = $lieu->getVilles()->getCodePostal();
            $rueLieu = $lieu->getRue();
            $this->client = $client;
            $response = $this->client->request(
                'GET',
                'http://nominatim.openstreetmap.org/search?format=json&limit=1&q='.$rueLieu.' '.$cpLieu.' '.$nomLieu
            );
            $content = $response->toArray();
            $latitude = $content[0]['lat'];
            $longitude = $content[0]['lon'];
            $lieu->setLatitude($latitude);
            $lieu->setlongitude($longitude);

            $this->addFlash('success', 'Création du lieu réalisé avec succès!');
            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('lieu_afficher', ['id'=> $lieu->getId() ]);
        }

        return $this->render('lieu/ajouter.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    #[Route('/lieu/modifier/{id}', name: 'lieu_modifier')]
    public function modifier(
        Request $request,
        EntityManagerInterface $entityManager,
        LieuRepository $lieuRepository,
        Lieu $id
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('lieu_liste');
        }

        $lieu = $lieuRepository->findOneBy(array('id'=>$id));
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()){
            $this->addFlash('success', 'Modification du lieu réalisé avec succès!');
            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('lieu_afficher', ['id'=> $lieu->getId() ]);
        }

        return $this->renderForm('lieu/modifier.html.twig',
            compact('formLieu')
        );
    }

    #[Route('/lieu', name: 'lieu_liste')]
    public function liste(
        LieuRepository $lieuRepository
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        $lieux = $lieuRepository->findAll();
        return $this->render('lieu/liste.html.twig',
            compact('lieux')
        );
    }

    #[Route('/lieu/afficher/{id}', name: 'lieu_afficher')]
    public function afficher(
        LieuRepository $lieuRepository,
        Lieu $id
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        $lieu = $lieuRepository->findOneBy(array('id'=>$id));

        if (!$this->getUser()->isAdministrateur()) {
            return $this->render('lieu/afficher_utilisateur.html.twig',
                compact('lieu')
            );
        } return $this->render('lieu/afficher.html.twig',
        compact('lieu')
    );
    }

    #[Route('/lieu/supprimer/{id}', name: 'lieu_supprimer')]
    public function supprimer(
        LieuRepository $lieuRepository,
        EntityManagerInterface $entityManager,
        Lieu $id
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('lieu_liste');
        }

        $lieu = $lieuRepository->findOneBy(array('id'=>$id));
        $entityManager->remove($lieu);
        $entityManager->flush();

        return $this->redirectToRoute('lieu_liste');
    }
}
