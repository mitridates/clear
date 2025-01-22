<?php
namespace App\Manager;
use App\Entity\FieldDefinition\Fieldvaluecode;
use App\Entity\FieldDefinition\Fieldvaluecodelang;
use Doctrine\ORM\EntityManagerInterface;

class FieldValueCodeManager extends AbstractManager
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, Fieldvaluecode::class);
    }

    public function countDisctinct(): array
    {
        return $this->em->createQueryBuilder('_fvc')
            ->select('COUNT(_fvc.id) AS codes')
            ->addSelect('COUNT(DISTINCT _fvc.field) AS fields')
            ->getQuery()->getResult()[0];
    }

    public function getFieldValueCodesTranslationsByField(array $ids): array
    {
        $alias= 'fdcl';
        $qb = $this->em->getRepository(Fieldvaluecodelang::class)->createQueryBuilder($alias);
        $qb->select($alias)
            ->add('where', $qb->expr()->in($alias.'.id', ':ids'))
            ->setParameter('ids', $ids);
//            ->setParameter('ids', $ids,  \Doctrine\DBAL\Connection::ARRAY_PARAM_OFFSET);
        return $qb->getQuery()->getArrayResult();
    }
}
