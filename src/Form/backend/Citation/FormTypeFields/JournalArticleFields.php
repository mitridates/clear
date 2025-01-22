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

final class JournalArticleFields extends AbstractFormFields  implements FormFieldsEventInterface
{
    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);
        $this->fields= array_merge(
            (new CitationEntityFields($builder))
                ->getNamedFields(['title', 'subtitle', 'containertitle', 'contributor', 'showsubtitle']),
            (new JsonHrefFields($builder))->getFields(),
            (new JsonPageFields($builder))->getFields(),
            (new JsonPublicationDateFields($builder))->getFields(),
            (new JsonBibliographyFields($builder))->getNamedFields(['publisher', 'issue']),
            (new JsonVolumeFields($builder))->getFields(),
        );

    }

    public static function setPostSubmitData(FormEvent $event): void
    {
        $form= $event->getForm();
        /** @var Citation $citation */
        $citation = $event->getData();
        $json= json_decode($citation->getJsondata(), true)??[];
        JsonPageFields::setPostSubmitJsonData($form, $json);
        JsonVolumeFields::setPostSubmitJsonData($form, $json);
        JsonPublicationDateFields::setPostSubmitJsonData($form, $json);
        JsonHrefFields::setPostSubmitJsonData($form, $json);
        JsonBibliographyFields::setPostSubmitJsonData($form, $json);

        if($citation->getType()!==Citation::JOURNAL_ARTICLE_TYPE){
            $citation->setType(Citation::JOURNAL_ARTICLE_TYPE);
        }

        $citation->setJsondata(json_encode($json));
    }


    /**
     *
     * The choices[] option MUST contain the POST value selected if exists or will send a FormError.
     * @param FormEvent $event
     * @return void
     */
    public static function setPreSubmitData(FormEvent $event): void
    {
        JsonPublicationDateFields::setPreSubmitData($event);
    }




}