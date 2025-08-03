<?php

namespace App\Citation\UI\Form\FormTypeFields;

use App\Citation\Domain\Entity\Citation;
use App\Citation\UI\Form\CommonFields\CitationEntityFields;
use App\Citation\UI\Form\CommonFields\JsonAccessDateFields;
use App\Citation\UI\Form\CommonFields\JsonBibliographyFields;
use App\Citation\UI\Form\CommonFields\JsonHrefFields;
use App\Citation\UI\Form\CommonFields\JsonPublicationDateFields;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class WebsiteFields //extends AbstractFormFields implements FormFieldsEventInterface
{

    public function __construct(FormBuilderInterface $builder)
    {
        $this->fields= array_merge(
            (new CitationEntityFields($builder))
                ->setFieldOptions('comment', ['label'=>'Annotation'])
                ->getNamedFields(['title', 'comment']),
            (new JsonPublicationDateFields($builder))->getFields(),
            (new JsonHrefFields($builder))
                ->setFieldOptions(
                    'url', ['required'=>true]
                )->getNamedFields(['url']),
            (new JsonAccessDateFields($builder))->getFields(),
            (new JsonBibliographyFields($builder))->getNamedFields(['publisher'])
        );

        foreach ($this->fields as $field) {
            call_user_func_array([$builder, 'add'], $field);
        }


        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'setPreSubmitData']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'setPostSubmitData']);
    }


    /**
     * @inheritDoc
     */
    public  function setPostSubmitData(FormEvent $event): void
    {
        $form= $event->getForm();
        /** @var Citation $citation */
        $citation = $event->getData();
        $json= json_decode($citation->getJsondata(), true)??[];
        JsonPublicationDateFields::setPostSubmitJsonData($form, $json);
        JsonHrefFields::setPostSubmitJsonData($form, $json);
        JsonAccessDateFields::setPostSubmitJsonData($form, $json);
        JsonBibliographyFields::setPostSubmitJsonData($form, $json);

        if($citation->getType()!==Citation::WEBSITE_TYPE){
            $citation->setType(Citation::WEBSITE_TYPE);
        }

        $citation->setJsondata(json_encode($json));
    }

    /**
     *
     * Set Choices with POST value if exists (else FormError).
     * @param FormEvent $event
     * @return void
     */
    public  function setPreSubmitData(FormEvent $event): void
    {
        JsonPublicationDateFields::setPreSubmitData($event);
        JsonAccessDateFields::setPreSubmitData($event);
    }
}