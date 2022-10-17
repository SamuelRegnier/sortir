<?php

namespace App\Command;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'majEtat',
    description: 'Add a short description for your command',
)]
class MajEtatCommand extends Command
{
    protected static $defaultName = 'majEtat';
    private SortieRepository $sortieRepository;
    private EtatRepository $etatRepository;
    private EntityManagerInterface $manager;

    public function __construct(SortieRepository $sortieRepository,EtatRepository $etatRepository, EntityManagerInterface $manager)
    {
        parent::__construct(self::$defaultName);
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->manager = $manager;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sortiesOuvertes = $this->sortieRepository->findBy(['etats'=>2]);
        $sortiesCloturees = $this->sortieRepository->findBy(['etats'=>3]);
        $cloturee = $this->etatRepository->findOneBy(array('id'=> 3));
        $sortiesEnCours = $this->sortieRepository->findBy(['etats'=>4]);
        $enCours = $this->etatRepository->findOneBy(array('id'=> 4));
        $sortiesPassees = $this->sortieRepository->findBy(['etats'=>5]);
        $passee = $this->etatRepository->findOneBy(array('id'=> 5));
        $archivee = $this->etatRepository->findOneBy(array('id'=> 7));

        $date = date('Y-m-d H:i:s');
        $dateFormatee = strtotime($date);

        foreach ($sortiesOuvertes as $sortieOuverte) {
            //dd($date);
            //dd($sortieOuverte->getDateLimiteInscription());
            $dateSortieOuverte = $sortieOuverte->getDateLimiteInscription();
            $dateSortieOuverteFormatee = strtotime($dateSortieOuverte);
            if ($dateFormatee > $dateSortieOuverteFormatee) {
                dd($date);
                $sortieOuverte->setEtats($cloturee);
                $this->manager->persist($sortieOuverte);
                $this->manager->flush();
            }
        }

        //dd($cloturee);
        //dd($sortiesOuvertes);

        foreach ($sortiesCloturees as $sortieCloturee) {
            if ($date > $sortieCloturee->getDateHeureDebut()) {
                $sortieCloturee->setEtats($enCours);
                $this->manager->persist($sortieCloturee);
            }
        }
        foreach ($sortiesEnCours as $sortieEnCours) {
            if ($date > $sortieEnCours->getDateHeureDebut() + $sortieEnCours->getDuree()) {
                $sortieEnCours->setEtats($passee);
                $this->manager->persist($sortieEnCours);
            }
        }
        foreach ($sortiesPassees as $sortiePassee) {
            if ($date > $sortiePassee->getDateHeureDebut() + $sortiePassee->getDateHeureDebut()->add(new \DateInterval('P30D'))) {
                $sortieEnCours->setEtats($archivee);
                $this->manager->persist($sortiePassee);
            }
        }

        $this->manager->flush();

        return Command::SUCCESS;
    }
}
