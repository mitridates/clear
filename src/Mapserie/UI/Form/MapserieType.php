<?php
namespace App\Mapserie\UI\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapserieType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $fields= new MapserieFields();

        foreach ($fields->getFields() as $field){
            call_user_func_array([$builder, 'add'], $field);
        }
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'publisher'));
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>'mapserieForm'],
        ));
    }
}


