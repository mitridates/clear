<?php
namespace App\Form\backend\Citation;
use App\Form\backend\Citation\FormTypeFields\WebpageFields;
use App\Form\backend\Citation\FormTypeFields\WebsiteFields;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebpageType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->getFormFactory();
        $fields= new WebpageFields($builder);

        foreach ($fields->getFields() as $field) {
            call_user_func_array([$builder, 'add'], $field);
        }
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [WebpageFields::class, 'setPreSubmitData']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [WebpageFields::class, 'setPostSubmitData']);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr'=> [
                'data-type'=>'webpage',
                'autocomplete'=>"off",
                'id'=>'WebsiteCitationForm'
            ]
        ));
    }
}


