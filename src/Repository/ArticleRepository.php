<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }



    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @return article[]
     * @param $max prend en parametre un entier definissant le maximum de resultat
     */
    public function FindLatest($max):array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.Created_at', 'DESC')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();

    }

    /**
     * @param $tri string
     * @param $value string
     *@return article[]
          */
    public function FindAllOrdered(string $tri, string $value):array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.'.$tri,$value )
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user
     * @return article[]
     */
    public function FindAllLikedArticles($user):array
    {
        $query = $this->_em->createQueryBuilder();
        $query
            ->select('a')
            ->from(Article::class, 'a')
            ->join('a.likes', 'l')
            ->where('l.user = :user')
            ->orderBy('a.Created_at', 'desc')
            ->setParameter('user', $user) ;
        return $query->getQuery()->getResult();
    }
}


