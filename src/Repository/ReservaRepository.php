<?php

namespace App\Repository;

use App\Entity\Reserva;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reserva>
 *
 * @method Reserva|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reserva|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reserva[]    findAll()
 * @method Reserva[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reserva::class);
    }

    public function findAllByIdUser(int $idUser): array
    {
    
        return $this->createQueryBuilder('r')
            
            ->andWhere('r.usuario = :idUser')
            ->setParameter('idUser', $idUser)
            ->getQuery()
            ->getResult();
    }

    public function findByTourAndUser(int $idTour, int $idUser): Reserva
    {
    
        return $this->createQueryBuilder('r')
            ->andWhere('r.tour = :idTour')
            ->andWhere('r.usuario = :idUser')
            ->setParameter('idUser', $idUser)
            ->setParameter('idTour', $idTour)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function existeReserva(int $idTour, int $idUser): bool
    {
    
        $reserva= $this->createQueryBuilder('r')
            ->andWhere('r.tour = :idTour')
            ->andWhere('r.usuario = :idUser')
            ->setParameter('idUser', $idUser)
            ->setParameter('idTour', $idTour)
            ->getQuery()
            ->getOneOrNullResult();
        if($reserva !=null)
            return true;
        return false;

    }
 
}
