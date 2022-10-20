<?php

namespace App\Repository;

use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\DateType;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
    public function findByFiltre($organisateur, $site, $estInscrit, $pasInscrit, $passee, $nom, $date1, $date2): array
    {
        $db = $this->createQueryBuilder('s')
            ->select('s');

        if ($organisateur != null) {
            $db->where('s.organisateur = :organisateur');
            $db->setParameter('organisateur', $organisateur);
        }
        if ($site != null) {
            $db->andWhere('s.site = :site');
            $db->setParameter('site', $site);
        }
        if ($estInscrit) {
            $db->join('s.inscriptions_sortie', 'ins');
            $db->andWhere('ins.participant = :inscrit');
            $db->setParameter('inscrit', $estInscrit);
        }

        if ($passee) {
            $db->andWhere('s.etats = :etat');
            $db->setParameter('etat', $passee);
        }
        if ($nom) {
            $db->andWhere('s.nom LIKE :nom');
            $db->setParameter('nom', $nom);
        }
        if ($date1 != null && $date2 != null) {
            $db->andWhere('s.dateHeureDebut > :date1');
            $db->andWhere('s.dateHeureDebut < :date2');
            $db->setParameter('date1', $date1);
            $db->setParameter('date2', $date2);
        }

        $db->getQuery();
        $response = $db->getQuery()->getResult();

        if ($pasInscrit) {
            $inscriptions = $pasInscrit->getInscriptionsParticipant();
            foreach ($inscriptions as $inscription) {
                $inscrit = $this->findOneBy(['id' => $inscription->getSortie()]);
                $tab[] = $inscrit;
            }
            if ($estInscrit) {
                return array_merge($response, $tab);
            } else {
                return array_diff($response, $tab);

            }
        }
        return $response;
    }


//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
