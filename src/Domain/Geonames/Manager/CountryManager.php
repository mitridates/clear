<?php
namespace App\Domain\Geonames\Manager;
use App\Entity\Geonames\{App\Domain\Geonames\Entity\Admin1,
    App\Domain\Geonames\Entity\Admin2,
    App\Domain\Geonames\Entity\Admin3,
    App\Domain\Geonames\Entity\Country};
use App\Manager\AbstractManager;
use Doctrine\ORM\EntityManagerInterface;

class CountryManager extends AbstractManager
{
    public function __construct(protected EntityManagerInterface $em)
    {
        parent::__construct($em, \App\Domain\Geonames\Entity\Country::class);
    }
//
//    public function getCountries(): array
//    {
//        $admin1Dql = $this->em->getRepository(Admin1::class)
//            ->createQueryBuilder('a1')
//            ->select('count(a1)')
//            ->where('a1.country= country.id')
//            ->getDQL()
//        ;
//        $admin2Dql = $this->em->getRepository(Admin2::class)
//            ->createQueryBuilder('a2')
//            ->select('count(a2)')
//            ->where('a2.country= country.id')
//            ->getDQL()
//        ;
//        $admin3Dql = $this->em->getRepository(Admin3::class)
//            ->createQueryBuilder('a3')
//            ->select('count(a3)')
//            ->where('a3.country= country.id')
//            ->getDQL()
//        ;
//
//       return  $this->em->getRepository(Country::class)->
//        createQueryBuilder('country')
//            ->select('country.id', 'country.name')
//           ->addSelect('('.$admin1Dql.') AS admin1')
//           ->addSelect('('.$admin2Dql.') AS admin2')
//           ->addSelect('('.$admin3Dql.') AS admin3')
//           ->getQuery()
//            ->getArrayResult();
//    }
}
