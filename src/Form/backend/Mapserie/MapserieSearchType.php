<?php
namespace App\Form\backend\Mapserie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapserieSearchType extends AbstractType
{
    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $factory = $builder->getFormFactory();
        $fields= new MapserieFields();
        foreach ($fields->getNamedFields(['name', 'scale', 'lengthunits', 'maptype']) as $field){
            $field[2]['required']=false;
            call_user_func_array([$builder, 'add'], $field);
        }
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'publisher'));
     }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}
