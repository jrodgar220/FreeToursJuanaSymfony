<?php

namespace App\Repository;

use App\Entity\Tour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tour>
 *
 * @method Tour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tour[]    findAll()
 * @method Tour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tour::class);
    }

    public function findToursByFecha(string $fecha): array
    {
        $fechaFormateada = \DateTimeImmutable::createFromFormat('d/m/Y', str_replace("\\/", "/", $fecha));
        $fechaFormateadaBD = $fechaFormateada->format('Y-m-d');
    
        return $this->createQueryBuilder('t')
            
            ->andWhere('t.fecha = :fecha')
            ->setParameter('fecha', $fechaFormateadaBD)
            ->getQuery()
            ->getResult();
    }
    
    public function findToursByGuia(int $idGuia): array
    {
        
        return $this->createQueryBuilder('t')
            
            ->andWhere('t.guia = :guia')
            ->setParameter('guia', $idGuia)
            ->getQuery()
            ->getResult();
    }
    
//    /**
//     * @return Tour[] Returns an array of Tour objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tour
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
