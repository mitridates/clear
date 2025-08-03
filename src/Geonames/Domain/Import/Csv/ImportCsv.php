<?php

namespace App\Geonames\Domain\Import\Csv;

use App\Fielddefinition\Domain\Entity\Fielddefinition;
use App\Fielddefinition\Domain\Entity\Fielddefinitionlang;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use App\Fielddefinition\Domain\Entity\Fieldvaluecodelang;
use App\Organisation\Domain\Entity\Organisation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class ImportCsv
{

    public function __construct(private readonly string $dir)
    {
    }

    public function load(string $filename, string $type, EntityManagerInterface $em): void
    {
        $file= $this->dir.$filename;

        if(!is_file($this->dir.$file)) throw new \exception(sprintf('File "%s" not found in "%s". See config.',$filename, $this->dir));

        switch ($type){
            case 'fielddefinition':
                $csvToEntity = new FieldDefinitionCsv($em->getRepository(Fielddefinition::class));
                break;
            case 'fielddefinitionlang':
                $csvToEntity = new FieldDefinitionLangCsv($em->getRepository(Fielddefinitionlang::class), $em->getRepository(Fielddefinition::class));
                break;
            case 'fieldvaluecode':
                $csvToEntity = new FieldValueCodeCsv($em->getRepository(Fieldvaluecode::class));
                break;
            case 'fieldvaluecodelang':
                $csvToEntity = new FieldValueCodeLangCsv($em->getRepository(Fieldvaluecodelang::class), $em->getRepository(Fieldvaluecode::class));
                break;
            case 'organisation':
                $csvToEntity = new OrganisationCsv($em->getRepository(Organisation::class), $em);
                break;
        }

        $this->handleFile($file, $csvToEntity, $em);

    }

    private function handleFile(string $file, AbstractCsv $csvToEntity, EntityManagerInterface $em): void
    {
        $handle = fopen($file,'r');

        while ( ($data = fgetcsv($handle) ) !== FALSE )
        {
            if(str_starts_with($data[0], '#') || (count($data)===1) && $data[0]==null) continue;//skip comments, empty line
            $registry = $csvToEntity->getEntity($data);
            if($registry instanceof Organisation){
                $metadata=  $em->getClassMetadata(Organisation::class);
                $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
            }
            $em->persist($registry);
            $em->flush();
        }
    }
}