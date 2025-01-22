<?php
namespace App\Form\backend\Citation;
use App\Form\backend\Citation\FormTypeFields\BookCarpetFields;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookCarpetType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->getFormFactory();
        $fields= new BookCarpetFields($builder);

        foreach ($fields->getFields() as $field) {
            call_user_func_array([$builder, 'add'], $field);
        }
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [BookCarpetFields::class, 'setPreSubmitData']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [BookCarpetFields::class, 'setPostSubmitData']);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr'=> [
                'data-type'=>'book-carpet',
                'autocomplete'=>"off",
                'id'=>'BookCarpetCitationForm'
            ]
        ));
    }
}


