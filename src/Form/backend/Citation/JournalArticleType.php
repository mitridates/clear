<?php
namespace App\Form\backend\Citation;
use App\Form\backend\Citation\FormTypeFields\JournalArticleFields;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalArticleType extends AbstractType
{
    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields= new JournalArticleFields($builder);

        foreach ($fields->getFields() as $field){
            call_user_func_array([$builder, 'add'], $field);
        }
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [JournalArticleFields::class, 'setPreSubmitData']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [JournalArticleFields::class, 'setPostSubmitData']);
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> [
                'data-type'=>'journal-article',
                'autocomplete'=>"off",
                'id'=>'JournalArticleCitationForm'
            ]
        ));
    }
}


