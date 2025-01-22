<?php
namespace App\Repository\Geonames;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class CommonRepository extends EntityRepository
{
    public function countAll(): int
    {
        try {
            return $this->createQueryBuilder('foo')->select('count(foo)')->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException) {
            return 0;
        }
    }

    public function getRegistry(string|object $identifier): ?object
    {
        return gettype($identifier)==='object' ? $this->find($identifier->getId()) : $this->find($identifier);
    }
}