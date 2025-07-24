<?php

namespace App\Shared\Doctrine\Orm\Id;

use App\Entity\SystemParameter;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

/**
 * Custom ID generator according to UISIC
 * @see http://www.uisic.uis-speleo.org/exchange/exchprop.html#rident
 */
class CavernIdGenerator  extends AbstractIdGenerator
{

    /**
     * Custom ID generator.
     * @param EntityManager $em
     * @param object $entity
     * @return string
     * @throws Exception
     */
    public function generate(EntityManager $em , $entity): string
    {
        /**
         * @var SystemParameter $sys
         */
        $sys= $em->getRepository(SystemParameter::class)->findOneBy(['active'=>true]);

        if(!$sys){
            throw new Exception("No se han encontrado parámetros de administración del sistema!");
        }

        if(!$organisation = $sys->getOrganisationdbm()){
            throw new Exception("No se han encontrado organización administradora del sistema");
        }

        $ID= $organisation->getId();
        $length = strlen($ID);

        if($length!==10){
            throw new Exception(sprintf('Invalid DBM manager ID length (%s): %s, 10 chars expected.', $length, $ID));
        }

        $prefix =  substr($ID, 0,5);//{2 chars}country +{3 chars} organisation

        return $prefix.$this->getNextSerial($em, $entity , $prefix);
   }

    /**
     * Next serial
     * @param EntityManager $em
     * @param object $entity
     * @param string $prefix Prefix: {2 chars}country + {3 chars} organisation
     * @return string
     * @throws MappingException|NoResultException|NonUniqueResultException|NotSupported
     */
    protected function getNextSerial(EntityManager $em, object $entity, string $prefix): string
    {
        $meta = $em->getClassMetadata(get_class($entity));
        $identifier = $meta->getSingleIdentifierFieldName();
        $repository = $em->getRepository(get_class($entity));

        $max_id = $repository->createQueryBuilder('o')
            ->select('MAX (o.'.$identifier.') as max_id')
            ->where('o.'.$identifier.' LIKE :id')
            ->setParameter(':id', $prefix.'%')
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_ARRAY)['max_id'];

        if(NULL == $max_id){
            $max_id = 0;
        }

        $max_serial =  (int)substr($max_id, 5, 5);
        $next_serial = $max_serial+1;
        if($next_serial>99999){
            throw new Exception('Se ha alcanzado límite de registros disponibles "%s" para la entidad %s y no es posible crear nuevos registros.',
                $max_serial, $repository->getClassName());
        }
        return \str_pad($next_serial, 5, 0, STR_PAD_LEFT);
    }
}
