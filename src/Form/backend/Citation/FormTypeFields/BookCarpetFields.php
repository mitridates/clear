<?php

namespace App\Form\backend\Citation\FormTypeFields;

use App\Entity\Citation\Citation;
use App\Form\backend\Citation\CommonFields\CitationEntityFields;
use App\Form\backend\Citation\CommonFields\JsonBibliographyFields;
use App\Form\backend\Citation\CommonFields\JsonHrefFields;
use App\Form\backend\Citation\CommonFields\JsonPageFields;
use App\Form\backend\Citation\CommonFields\JsonPublicationDateFields;
use App\Form\backend\Citation\CommonFields\JsonVolumeFields;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\FormFieldsEventInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;

final class BookCarpetFields extends AbstractFormFields implements FormFieldsEventInterface
{

    public function __construct(FormBuilderInterface $builder)
    {
        $this->fields= array_merge(
            (new CitationEntityFields($builder))
                ->setFieldOptions('comment', ['label'=>'Annotation'])
                ->setFieldOptions('title', ['label'=>'Charpet title'])
                ->setFieldOptions('containertitle', ['label'=>'Book title', 'required'=>true])
                ->getNamedFields(
                    ['title', 'subtitle', 'containertitle', 'showsubtitle', 'contributor', 'country','comment', 'content']),
            (new JsonPublicationDateFields($builder))->getFields(),
            (new JsonHrefFields($builder))->getFields(),
            (new JsonPageFields($builder))->getFields(),
            (new JsonVolumeFields($builder))->getFields(),
//            (new JsonPageFields($builder))->getFields(),
            (new JsonBibliographyFields($builder))
                ->getNamedFields(['isbn', 'publisher', 'copyright', 'ldn', 'edition', 'medium'])
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
        JsonPageFields::setPostSubmitJsonData($form, $json);
        JsonBibliographyFields::setPostSubmitJsonData($form, $json);

        if($citation->getType()!==Citation::BOOK_CARPET_TYPE){
            $citation->setType(Citation::BOOK_CARPET_TYPE);
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