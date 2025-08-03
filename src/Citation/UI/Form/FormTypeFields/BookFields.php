<?php

namespace App\Citation\UI\Form\FormTypeFields;

use App\Citation\Domain\Entity\Citation;
use App\Citation\UI\Form\CommonFields\CitationEntityFields;
use App\Citation\UI\Form\CommonFields\JsonBibliographyFields;
use App\Citation\UI\Form\CommonFields\JsonHrefFields;
use App\Citation\UI\Form\CommonFields\JsonPublicationDateFields;
use App\Citation\UI\Form\CommonFields\JsonVolumeFields;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\FormFieldsEventInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;

final class BookFields extends AbstractFormFields implements FormFieldsEventInterface
{

    public function __construct(FormBuilderInterface $builder)
    {
        $this->fields= array_merge(
            (new CitationEntityFields($builder))
                ->setFieldOptions('comment', ['label'=>'Annotation'])
                ->getNamedFields(
                    ['title', 'subtitle', 'showsubtitle', 'contributor', 'country','comment', 'content']),
            (new JsonPublicationDateFields($builder))->getFields(),
            (new JsonHrefFields($builder))->getFields(),
            (new JsonVolumeFields($builder))->getFields(),
//            (new JsonPageFields($builder))->getFields(),
            (new JsonBibliographyFields($builder))
                ->getNamedFields(['isbn', 'publisher', 'issue', 'copyright', 'ldn', 'edition', 'medium'])
        );
    }


    /**
     * @inheritDoc
     */
    public static function setPostSubmitData(FormEvent $event): void
    {
        $form= $event->getForm();
        /** @var Citation $citation */
        $citation = $event->getData();
        $json= json_decode($citation->getJsondata(), true)??[];
        JsonPublicationDateFields::setPostSubmitJsonData($form, $json);
        JsonHrefFields::setPostSubmitJsonData($form, $json);
        JsonVolumeFields::setPostSubmitJsonData($form, $json);
//        JsonPageFields::setPostSubmitJsonData($form, $json);
        JsonBibliographyFields::setPostSubmitJsonData($form, $json);

        if($citation->getType()!==Citation::BOOK_TYPE){
            $citation->setType(Citation::BOOK_TYPE);
        }
        
        $citation->setJsondata(json_encode($json));
    }

    /**
     * Set Choices with POST value if exists (else FormError).
     * @param FormEvent $event
     * @return void
     */
    public static function setPreSubmitData(FormEvent $event): void
    {
        JsonPublicationDateFields::setPreSubmitData($event);
    }
}