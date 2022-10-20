<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    #[Route('/site/ajouter', name: 'site_ajouter')]
    public function ajouter(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('site_liste');
        }

        $site = new Site();

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Création du site réalisé avec succès!');
            $entityManager->persist($site);
            $entityManager->flush();
            return $this->redirectToRoute('site_afficher', ['id'=> $site->getId() ]);
        }

        return $this->render('site/ajouter.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    #[Route('/site/modifier/{id}', name: 'site_modifier')]
    public function modifier(
        Request $request,
        EntityManagerInterface $entityManager,
        SiteRepository $siteRepository,
        Site $id
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }

        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('site_liste');
        }

        $site = $siteRepository->findOneBy(array('id'=>$id));
        $formSite = $this->createForm(SiteType::class, $site);
        $formSite->handleRequest($request);

        if ($formSite->isSubmitted() && $formSite->isValid()){
            $this->addFlash('success', 'Modification du site réalisé avec succès!');
            $entityManager->persist($site);
            $entityManager->flush();
            return $this->redirectToRoute('site_afficher', ['id'=> $site->getId() ]);
        }

        return $this->renderForm('site/modifier.html.twig',
            compact('formSite')
        );
    }

    #[Route('/site', name: 'site_liste')]
    public function liste(
        SiteRepository $siteRepository
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        $sites = $siteRepository->findAll();

        return $this->render('site/liste.html.twig',
            compact('sites')
        );
    }

    #[Route('/site/afficher/{id}', name: 'site_afficher')]
    public function afficher(
        SiteRepository $siteRepository,
        Site $id
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        $site = $siteRepository->findOneBy(array('id' => $id));

        if (!$this->getUser()->isAdministrateur()) {
            return $this->render('site/afficher_utilisateur.html.twig',
                compact('site')
            );
        } return $this->render('site/afficher.html.twig',
            compact('site')
        );
    }

    #[Route('/site/supprimer/{id}', name: 'site_supprimer')]
    public function supprimer(
        SiteRepository $siteRepository,
        EntityManagerInterface $entityManager,
        Site $id
    ): Response
    {
        if(!$this->getUser()){
            $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isAdministrateur()){
            return $this->redirectToRoute('site_liste');
        }

        $site = $siteRepository->findOneBy(array('id'=>$id));
        $entityManager->remove($site);
        $entityManager->flush();

        return $this->redirectToRoute('site_liste');
    }
}
