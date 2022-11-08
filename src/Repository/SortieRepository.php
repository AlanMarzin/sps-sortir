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
        $qb->addSelect('sortie')
            ->leftJoin('sortie.campus', 'campus')
            ->addSelect('campus')
            ->leftJoin('sortie.etat', 'etat')
            ->addSelect('etat')
            ->leftJoin('sortie.organisateur', 'organisateur')
            ->addSelect('organisateur')
            ->addSelect('inscrits')
            ->leftJoin('sortie.inscrits', 'inscrits')
            ->where('etat.libelle != :historisee')
            ->setParameter('historisee', 'historisée');

            // création de l'expression AND (en création et créée par l'ut en cours)
            $andModule = $qb->expr()->andX();
            $andModule->add($qb->expr()->eq('etat.libelle', ':encreation'));
            $andModule->add($qb->expr()->eq('sortie.organisateur', ':id'));

            // création de l'expression OR (pas en création ou en création et créée par l'ut en cours)
            $orModule = $qb->expr()->orx();
            $orModule->add($qb->expr()->neq('etat.libelle', ':encreation'));
            $orModule->add($andModule);

            // ajout de l'expression à la requête
            $qb->andWhere($orModule)
                ->setParameter('encreation', 'en création')
                ->setParameter('encreation', 'en création')
                ->setParameter('id', $currentUser->getId());

        if ($filtres->getCampus()) {
            $qb->andWhere('campus.nom = :campus')
                ->setParameter('campus', $filtres->getCampus()->getNom());
        }

        if ($filtres->getNomRecherche()) {
            $qb->andWhere('LOWER(sortie.nom) LIKE :nomRecherche')
                ->setParameter('nomRecherche', '%' . $filtres->getNomRecherche() . '%');
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
                ->setParameter('user', $currentUser)
                ->andWhere('etat.libelle = :ouverte')
                ->setParameter('ouverte', 'ouverte');
        }

        if ($filtres->getIsPassee()) {
            $qb->andWhere('etat.libelle != :encours')
                ->setParameter('encours', 'en cours')
                ->andWhere('sortie.dateHeureDebut < CURRENT_DATE()');
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllAffichables(UserInterface $currentUser): array
    {
        $qb = $this->createQueryBuilder('sortie');

        $qb->addSelect('sortie')
            ->leftJoin('sortie.campus', 'campus')
            ->addSelect('campus')
            ->leftJoin('sortie.etat', 'etat')
            ->addSelect('etat')
            ->leftJoin('sortie.organisateur', 'organisateur')
            ->addSelect('organisateur')
            ->addSelect('inscrits')
            ->leftJoin('sortie.inscrits', 'inscrits')
            // filtrer sur le campus de l'utilisateur connecté
            ->andWhere('campus.nom = :campus')
            ->setParameter('campus', $currentUser->getCampus()->getNom())
            // exclure les sorties historisées
            ->andWhere('etat.libelle != :historisee')
            ->setParameter('historisee', 'historisée')
            // exclure les sorties en création si ce ne sont pas celles de l'ut connecté
            ->andWhere('etat.libelle != :encreation AND sortie.organisateur != :id')
            ->setParameter('encreation', 'en création')
            ->setParameter('id', $currentUser->getId());

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
