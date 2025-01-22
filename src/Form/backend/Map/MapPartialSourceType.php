<?php
namespace App\Form\backend\Map;
use App\Form\backend\Map\FormTypeFields\MapFields;
use App\Form\backend\Map\Model\PartialFormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapPartialSourceType extends AbstractType implements PartialFormTypeInterface
{
    const FIELDS=['sourceifnoid', 'sourcetype', 'sourcecountry','sourceorg'];


    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory= $builder->getFormFactory();
        $mapFields= new MapFields();
        $subscribers= ['sourcecountry', 'sourceorg' ];

        foreach (self::FIELDS as $name){
            if(in_array($name, $subscribers)){
                $builder->addEventSubscriber($mapFields->getSubscriber($factory, $name));
            }else{
                call_user_func_array([$builder, 'add'], $mapFields->getField($name));
            }
        }
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()],
            'clearForm' => false,
        ));
    }

    static function getFormTypeFieldNames(): array
    {
        return self::FIELDS;
    }
}


