<?php
namespace App\Map\UI\Form;
use App\Form\EventListener\AddAreaFieldSubscriber;
use App\Map\UI\Form\FormTypeFields\MapFields;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapType extends AbstractType
{
    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $fields= new MapFields();
        $notIn=array_merge(
            MapPartialSurveyType::getFieldNames(),
            MapPartialCoordinatesType::getFieldNames(),
            MapPartialSourceType::getFieldNames()
        );
       foreach ($fields->getFields() as $field){
            //partial form as details

//           $notIn= ['geogcoordsshown', 'geodeticdatum','heightdatum','grid'
//               , 'northlatitude', 'southlatitude', 'eastlongitude', 'westlongitude'];

            if(in_array($field[0], $notIn)) continue;

            call_user_func_array([$builder, 'add'], $field);
        }

//

        $adm=$fields->addAdministrativeDivisionsEventSubscriber($factory, $builder,
            ['field'=>'country'],
            ['field'=>'admin1'],
            ['field'=>'admin2'],
            ['field'=>'admin3']
        );


 //todo: subscribers... area está mal...
        $area= $fields->getSubscriberData('area');
        $areaSubscriber = new AddAreaFieldSubscriber($factory, $adm['country']->getCountry(), $adm['admin1']->getAdmin1(), $area[2]);
        $builder->addEventSubscriber($areaSubscriber);

        foreach (['mapserie', 'principalsurveyorid', 'principaldrafterid', 'surveygradeorg' ] as $item) {
            if(in_array($item, $notIn)) continue;
            $builder->addEventSubscriber($fields->getSubscriber($factory, $item));
        }

        foreach (['sourcecountry', 'sourceorg' ] as $item) {
            if(in_array($item, $notIn)) continue;
            $builder->addEventSubscriber($fields->getSubscriber($factory, $item));
        }

    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}


