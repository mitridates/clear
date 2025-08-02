<?php
namespace App\Manager;
use App\Domain\Fielddefinition\Entity\Fielddefinition;
use App\Domain\Fielddefinition\Entity\Fielddefinitionlang;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Domain\Fielddefinition\Entity\Fieldvaluecodelang;
use App\Domain\Geonames\Entity\Admin1;
use App\Domain\Geonames\Entity\Admin2;
use App\Domain\Geonames\Entity\Admin3;
use App\Domain\Geonames\Entity\Country;
use App\Domain\Organisation\Entity\Organisation;
use App\Domain\SystemParameter\Entity\SystemParameter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class SetupManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, SystemParameter::class);
    }

    public function getDataBaseStatus()
    {
        $sysCount = $this->em->getRepository(SystemParameter::class)->createQueryBuilder('sys_count')
            ->select('COUNT(DISTINCT sys_count.id)')->getDQL();
        $sysCountActive = $this->em->getRepository(SystemParameter::class)->createQueryBuilder('sys_count_active')
            ->select('COUNT(DISTINCT sys_count_active.id)')->where('sys_count_active.active= 1')
            ->setMaxResults(1)->getDQL();
        $sysCurrentActiveId = $this->em->getRepository(SystemParameter::class)->createQueryBuilder('sys_CurrentActiveId')
            ->select('sys_CurrentActiveId.id')
            ->where('sys_CurrentActiveId.active= 1')
            ->andWhere('sys_CurrentActiveId.country IS NOT NULL')
            ->andWhere('sys_CurrentActiveId.organisationdbm IS NOT NULL')
            ->orderBy('sys_CurrentActiveId.id', 'ASC')->setMaxResults(1)->getDQL();
        $fdCount = $this->em->getRepository(Fielddefinition::class)->createQueryBuilder('fd_count')
            ->select('COUNT(DISTINCT fd_count.id)')->getDQL();
        $fdlCount = $this->em->getRepository(Fielddefinitionlang::class)->createQueryBuilder('fdl_count')
            ->select('COUNT(DISTINCT CONCAT(IDENTITY(fdl_count.id), fdl_count.locale))')->getDQL();
        $fdlCountDefinitions = $this->em->getRepository(Fielddefinitionlang::class)->createQueryBuilder('fdl_count_definitions')
            ->select('COUNT(DISTINCT IDENTITY(fdl_count_definitions.id))')->getDQL();
        $fdlCountLocales= $this->em->getRepository(Fielddefinitionlang::class)->createQueryBuilder('fdl_count_locales')
            ->select('COUNT(DISTINCT fdl_count_locales.locale)')->getDQL();
        $fvcCount = $this->em->getRepository(Fieldvaluecode::class)->createQueryBuilder('fvc_count')
            ->select('COUNT(DISTINCT fvc_count.id)')->getDQL();
        $fvcCountField = $this->em->getRepository(Fieldvaluecode::class)->createQueryBuilder('fvc_count_field')
            ->select('COUNT(DISTINCT fvc_count_field.field)')->getDQL();
        $fvclCount = $this->em->getRepository(Fieldvaluecodelang::class)->createQueryBuilder('fvc_Lang_count')
            ->select('COUNT(DISTINCT CONCAT(IDENTITY(fvc_Lang_count.id), fvc_Lang_count.locale))')->getDQL();
        $fvclCountLocales= $this->em->getRepository(Fieldvaluecodelang::class)->createQueryBuilder('fvcLang_count_locales')
            ->select('COUNT(DISTINCT fvcLang_count_locales.locale)')->getDQL();
        $orgCount = $this->em->getRepository(Organisation::class)->createQueryBuilder('org_count')
            ->select('COUNT(DISTINCT org_count.id)')->getDQL();




        $result=  $this->em->getRepository(Country::class)->
        createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.id) AS countryCount')
            ->addSelect('('.$sysCount.') AS systemParameterCount')
            ->addSelect('('.$sysCountActive.') AS systemParameterCountActive')
            ->addSelect('('.$sysCurrentActiveId.') AS systemParameterCurrentActiveId')
            ->addSelect('('.$fdCount.') AS fdCount')
            ->addSelect('('.$fdlCount.') AS fdlCount')
            ->addSelect('('.$fdlCountDefinitions.') AS fdlCountDefinitions')
            ->addSelect('('.$fdlCountLocales.') AS fdlCountLocales')
            ->addSelect('('.$fvcCount.') AS fvcCount')
            ->addSelect('('.$fvcCountField.') AS fvcCountField')
            ->addSelect('('.$fvclCount.') AS fvclCount')
            ->addSelect('('.$fvclCountLocales.') AS fvclCountLocales')
            ->addSelect('('.$orgCount.') AS orgCount')
            ->getQuery()->getResult()[0];

        $result['geonames']= ($result['countryCount'])? $this->getGeonamesData():[];
        $result['systemParameter']= ($result['systemParameterCount'])? $this->getSystemParameter():[];

        return $result;

    }

    public function getGeonamesData(): array
    {

        $admin1Dql = $this->em->getRepository(Admin1::class)
            ->createQueryBuilder('a1')
            ->select('count(a1)')
            ->where('a1.country= country.id')
            ->getDQL()
        ;
        $admin2Dql = $this->em->getRepository(Admin2::class)
            ->createQueryBuilder('a2')
            ->select('count(a2)')
            ->where('a2.country= country.id')
            ->getDQL()
        ;
        $admin3Dql = $this->em->getRepository(Admin3::class)
            ->createQueryBuilder('a3')
            ->select('count(a3)')
            ->where('a3.country= country.id')
            ->getDQL()
        ;

        return $this->em->getRepository(Country::class)->
        createQueryBuilder('country')
            ->select('country.id', 'country.name')
            ->addSelect('('.$admin1Dql.') AS admin1')
            ->addSelect('('.$admin2Dql.') AS admin2')
            ->addSelect('('.$admin3Dql.') AS admin3')
            ->getQuery()
            ->getArrayResult();
    }


    /**
     * @throws NonUniqueResultException
     */
    public function getSystemParameter(): array
    {
        return $this->em->createQueryBuilder()
            ->select('s.name',
                's.language',
                's.name',
                's.mapdir',
                'o.id AS dbm_org_id',
                'o.name AS dbm_org_name',
                'IDENTITY(s.country) AS country'
            )
            ->from(SystemParameter::class, 's')
            ->leftJoin('s.organisationdbm', 'o')
            ->where('s.active=1 ')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
