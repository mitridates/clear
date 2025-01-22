<?php
namespace App\Form\backend\Specie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecieSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields= new SpecieFields();
        foreach ($fields->getFields() as $field){
            $field[2]['required']= false;
            call_user_func_array([$builder, 'add'], $field);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}
