<?php
namespace App\Form\backend\setup;
use App\Domain\Fielddefinition\Entity\Fieldvaluecode;
use App\Domain\Organisation\Entity\Organisation;
use App\Form\EventListener\AddAdmin1FieldSubscriber;
use App\Form\EventListener\AddAdmin2FieldSubscriber;
use App\Form\EventListener\AddAdmin3FieldSubscriber;
use App\Form\EventListener\AddCountryFieldSubscriber;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{AbstractType, FormBuilderInterface, FormEvent, FormEvents};
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Generate first organisation
 */
class InstallOrganisationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();

        $countrySubscriber = new AddCountryFieldSubscriber($factory, ['options'=>['required' => true,'attr'=>['field_id'=>376]]]);
        $builder->addEventSubscriber($countrySubscriber);

        $admin1Subscriber = new AddAdmin1FieldSubscriber($factory, $countrySubscriber->getCountry(), ['options'=>['attr'=>['field_id'=>377]]]);
        $builder->addEventSubscriber($admin1Subscriber);

        $admin2Subscriber = new AddAdmin2FieldSubscriber($factory, $admin1Subscriber->getCountry(), $admin1Subscriber->getAdmin1());
        $builder->addEventSubscriber($admin2Subscriber);

        $builder->addEventSubscriber(new AddAdmin3FieldSubscriber($factory, $admin2Subscriber->getAdmin2()));


        foreach(explode(',', 'code:178,initials:390,name:391') as $el)
        {
            $field = explode(':', $el);
            $arr = ['attr'=>['field_id'=>$field[1]]];
            $builder->add($field[0], NULL, $arr);
        }



        foreach(explode(',','type:381,coverage:393,grouping:394') as $el){
            $field = explode(':', $el);
            $builder->add(
                $field[0], EntityType::class, array(
                    'class'=>Fieldvaluecode::class,
                    'attr'=>array('field_id'=>$field[1]),
                    'required' => false,//show empty option
                    'choice_label' => 'value',
                    'choice_value'=>'id',
                    'query_builder' => function(EntityRepository $e) use ($field){
                        return $e->createQueryBuilder('f')
                            ->select('f')
                            ->where('f.field = :field')
                            ->orderBy('f.value', 'ASC')
                            ->setParameter('field', $field[1]);
                    })
            );
        }

        $builder->add('code', NULL, array('attr'=>array('field_id'=>391),'required' => true) )
            ->add('code', NULL, array(
                'attr'=>array(
                    'maxlength'=> 3,
                    'pattern'=>'[A-Z]{3}',
                    'title'=>'ABC',
                    'field_id'=>178
                ),'required' => true) );

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event){
            /**
             * @var Organisation $entity
             */
            $entity = $event->getData();
            $entity->setIsgenerator(true);
            $reflection = new \ReflectionClass($entity);
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($entity,
                $entity->getCountry()->getId().//string 2
                strtoupper($entity->getCode()).//string 3
                '00001'//string 5
            );
        });

    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Organisation::class
        ));
    }
}


