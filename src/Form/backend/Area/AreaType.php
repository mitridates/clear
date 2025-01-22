<?php
namespace App\Form\backend\Area;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaType extends AbstractType
{
    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
        $fields= new AreaFields();

        foreach ($fields->getFields() as $field){
            call_user_func_array([$builder, 'add'], $field);
        }
        $fields->addAdministrativeDivisionsEventSubscriber(
            $factory,
            $builder,
            ['field'=>'country'],
            ['field'=>'admin1']
        );
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}


