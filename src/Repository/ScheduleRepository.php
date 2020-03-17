<?php

namespace App\Repository;

use App\Entity\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }
	
	public function findAll()
    {
        return $this->findBy(array(), array('status' => 'DESC'));
    }
	
	public function findByCityStart(int $idCityStart){
		$qb = $this->createQueryBuilder('t');
		$qb->select('t')
			->where('t.cityStart = :city_start_id')
			->setParameter('city_start_id', $idCityStart)
		;
		
		return $qb->getQuery()->getResult();
	}
	
	public function findByCityEnd(int $idCityEnd){
		$qb = $this->createQueryBuilder('t');
		$qb->select('t')
			->where('t.cityEnd = :city_end_id')
			->setParameter('city_end_id', $idCityEnd)
		;
		
		return $qb->getQuery()->getResult();
	}

    // /**
    //  * @return Schedule[] Returns an array of Schedule objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Schedule
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
