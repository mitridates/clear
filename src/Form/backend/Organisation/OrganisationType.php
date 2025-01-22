<?php
namespace App\Form\backend\Organisation;
use App\Entity\Organisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $fields= new OrganisationFields();

        /**
         * @var Organisation $organisation
         */
        $organisation = $builder->getData();

        foreach ($fields->getFields() as $field){
            call_user_func_array([$builder, 'add'], $field);
        }
        $fields->addAdministrativeDivisionsEventSubscriber(
            $factory,
            $builder,
        ['field'=>'country','options'=>['options'=>['required' => false]]],
        ['field'=>'admin1','options'=>['options'=>['required' => false]]],
        ['field'=>'admin2','options'=>['options'=>['required' => false]]],
        ['field'=>'admin3','options'=>['options'=>['required' => false]]]
        );
        $fields->addAdministrativeDivisionsEventSubscriber(
            $factory,
            $builder,
            ['field'=>'countryaddress','options'=>['options'=>['required' => false]]],
            ['field'=>'admin1address','options'=>['options'=>['required' => false]]],
            ['field'=>'admin2address','options'=>['options'=>['required' => false]]],
            ['field'=>'admin3address','options'=>['options'=>['required' => false]]],
        );

        $builder->addEventSubscriber($fields->getSubscriber($factory, 'currentidifdefunct'));

        //set defunct if child is not null
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event){
            /**
             * @var array $data
             */
            $data= $event->getData();
            if($data['currentidifdefunct']!=null || $data['defunctyear']!=null){
                $data['defunct']= true;
                $event->setData($data);
            }
        });

        //avoid circular reference
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($organisation){
            /**
             * @var Organisation $data
             */
            $data= $event->getData();
            if($data->getCurrentidifdefunct()!=null)
            {
                if($organisation->getId()== $data->getCurrentidifdefunct()->getId())
                {
                    $event->getForm()->get('currentidifdefunct')->addError(
                        new FormError('Circular reference', 'field.circular.reference', [], null)
                    );
                }
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>'organisationForm'],
        ));
    }
}


