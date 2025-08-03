<?php
namespace App\Map\UI\Form;
use App\Map\UI\Form\FormTypeFields\MapFields;
use App\Map\UI\Form\Model\PartialFormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapPartialSourceType extends AbstractType implements PartialFormTypeInterface
{

    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory= $builder->getFormFactory();
        $mapFields= new MapFields();
        $subscribers= ['sourcecountry', 'sourceorg' ];

        foreach (self::getFieldNames() as $name){
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

    static function getFieldNames(): array
    {
        return ['sourceifnoid', 'sourcetype', 'sourcecountry','sourceorg'];
    }
}


