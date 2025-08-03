<?php
namespace App\Specie\UI\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SpecieType extends AbstractType
{
    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fields= new SpecieFields();

        foreach ($fields->getFields() as $field){
            call_user_func_array([$builder, 'add'], $field);
        }
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>'specieForm'],
        ));
    }
}


