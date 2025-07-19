<?php
namespace App\Form\backend\Map;
use App\Form\backend\Map\FormTypeFields\MapFields;
use App\Form\backend\Map\Model\PartialFormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapPartialCoordinatesType extends AbstractType implements PartialFormTypeInterface
{
    const FIELDS=['geogcoordsshown', 'geodeticdatum','heightdatum',
            'grid', 'northlatitude', 'southlatitude', 'eastlongitude', 'westlongitude']
        ;

    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields= new MapFields();
        $formFields= self::FIELDS;
        foreach ($fields->getNamedFields($formFields) as $field){
            call_user_func_array([$builder, 'add'], $field);
        }

        if($options['clearForm']){
            $builder->addEventListener(FormEvents::PRE_SUBMIT, function($event) use ($formFields) {
                MapFields::deletePostSubmitData($event, $formFields);
            });
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
        return self::FIELDS;
    }
}


