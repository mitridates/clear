<?php

namespace App\Citation\UI\Form\CommonFields;

use App\Citation\Domain\Entity\Citation;
use App\Shared\UI\Form\FormFields\AbstractFormFields;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;

class CitationEntityFields extends AbstractFormFields
{

    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);
        $this->fields=[
            ['type', ChoiceType::class, [
                'choices'  => self::getCitationChoiceTypes(),
                'label'=>'Citation type',
                'required' => true,
            ],
            ],
            ['title', null, ['label'=>'Title']],
            ['containertitle', null, ['label'=>'Container title']],
            ['subtitle', null, ['label'=>'Subtitle']],
            ['showsubtitle', checkboxType::class, [
                'label'=>'Show subtitle',
                'required'=>false,
                'mapped'=>false,
                'data'=> boolval($citation->getSubtitle()),
                'attr'=>['class'=>'js-showsubtitle']
            ]],
            [ 'country', CountryType::class, [
                'label'=>'Choose Country',
                'placeholder' => 'Choose country',
                'preferred_choices' => ['ES'],
                'required' => false,
                'attr'=>['field_id'=>224]
            ]],
            ['contributor', null, ['label'=>'Contributor', 'attr'=>['class'=>'hidden']]],
            ['region', null, ['label'=>'Region']],
            ['city', null, ['label'=>'City']],
            ['comment', null, [
                'label'=>'Comment',
                'attr'=>['class'=>'js-comment']]
            ],
            ['content', null, [
                'label'=>'Content',
                'attr'=>['class'=>'js-comment']]
            ],
            ['pdf', null, ['label'=>'pdf']],
            ['url', null, ['label'=>'url']]
        ];
    }

    public static function getCitationChoiceTypes(): array
    {
        return [
            'Book'=>Citation::BOOK_TYPE,
            'Book charpet'=>Citation::BOOK_CARPET_TYPE,
            'Journal article'=>Citation::JOURNAL_ARTICLE_TYPE,
            'Webpage'=>Citation::WEBPAGE_TYPE,
            'Website'=>Citation::WEBSITE_TYPE,
            'Online article'=>Citation::ONLINE_ARTICLE_TYPE,
            'Online magazine article'=>Citation::ONLINE_MAGAZINE_ARTICLE_TYPE
        ];
    }

}