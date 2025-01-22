<?php

namespace App\Form\backend\Citation\FormTypeFields;

use App\Entity\Citation\Citation;
use App\Form\backend\Citation\CommonFields\CitationEntityFields;
use App\Form\backend\Citation\CommonFields\JsonAccessDateFields;
use App\Form\backend\Citation\CommonFields\JsonBibliographyFields;
use App\Form\backend\Citation\CommonFields\JsonHrefFields;
use App\Form\backend\Citation\CommonFields\JsonPublicationDateFields;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\FormFieldsEventInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;

final class WebpageFields extends AbstractFormFields implements FormFieldsEventInterface
{

    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);
        $this->fields= array_merge(

            (new CitationEntityFields($builder))
                ->setFieldOptions('comment', ['label'=>'Annotation'])
                ->setFieldOptions('content', ['label'=>'Description'])
                ->setFieldOptions('containertitle', ['label'=>'Website name'])
                ->getNamedFields(['title', 'subtitle', 'containertitle','comment', 'content', 'contributor', 'showsubtitle']),
            (new JsonPublicationDateFields($builder))->getFields(),
            (new JsonHrefFields($builder))
                ->setFieldOptions(
                    'url', ['required'=>true]
                )->getNamedFields(['url']),
//            (new JsonBibliographyFields($builder))->getNamedFields(['publisher']),
            (new JsonAccessDateFields($builder))->getFields()
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
        JsonAccessDateFields::setPostSubmitJsonData($form, $json);
        JsonBibliographyFields::setPostSubmitJsonData($form, $json);

        if($citation->getType()!==Citation::WEBPAGE_TYPE){
            $citation->setType(Citation::WEBPAGE_TYPE);
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
        JsonAccessDateFields::setPreSubmitData($event);
    }
}