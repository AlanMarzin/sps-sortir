<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Form\Model\FiltresSortiesFormModel;
use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function findByFiltresSorties(FiltresSortiesFormModel $filtres, UserInterface $currentUser): array
    {
        $qb = $this->createQueryBuilder('sortie');
        $qb->addSelect('sortie');

        if ($filtres->getCampus()) {
            $qb->leftJoin('sortie.campus', 'campus')
                ->addSelect('campus')
                ->andWhere('campus.nom = :campus')
                ->setParameter('campus', $filtres->getCampus()->getNom());
        }

        if ($filtres->getDateDebut()) {
            $qb->andWhere('sortie.dateHeureDebut >= :debut')
                ->setParameter('debut', $filtres->getDateDebut());
        }

        if ($filtres->getDateFin()) {
            $fin = date_add($filtres->getDateFin(), new DateInterval('P1D'));
            $qb->andWhere('sortie.dateHeureDebut <= :fin')
                ->setParameter('fin', $fin);
        }

        if ($filtres->getIsOrganisateur()) {
            $qb->andWhere('sortie.organisateur = :organisateur')
                ->setParameter('organisateur', $currentUser);
        }

        if ($filtres->getIsInscrit()) {
            $qb->andWhere(':user MEMBER OF sortie.inscrits')
                ->setParameter('user', $currentUser);
        }

        if ($filtres->getIsNotInscrit()) {
            $qb->andWhere(':user NOT MEMBER OF sortie.inscrits')
                ->setParameter('user', $currentUser);
        }

        if ($filtres->getIsPassee()) {
            $qb->leftJoin('sortie.etat', 'etat')
                ->addSelect('etat')
                ->andWhere('etat.libelle != :encours')
                ->setParameter('encours', 'en cours')
                ->andWhere('sortie.dateHeureDebut < CURRENT_DATE()');
        }

//        if ($filtres->getIsPassee()) {
//            $qb->andWhere(':dateFin < CURRENT_DATE()')
//                ->setParameter('dateFin', 'ADDTIME(sortie.dateHeureDebut + sortie.duree)');
//        }

        return $qb->getQuery()->getResult();
    }

    public function findAllAffichables(): array
    {
        $qb = $this->createQueryBuilder('sortie');
        $qb->addSelect('sortie')
            ->leftJoin('sortie.etat', 'etat')
            ->addSelect('etat')
            ->andWhere('etat.libelle != :creation')
            ->setParameter('creation', 'en création')
            ->andWhere('etat.libelle != :historisee')
            ->setParameter('historisee', 'historisée');

        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

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
