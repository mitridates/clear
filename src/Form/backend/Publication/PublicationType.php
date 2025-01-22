<?php
namespace App\Form\backend\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();

        foreach (PublicationFields::getFields() as $field){
            call_user_func_array([$builder, 'add'], $field);
        }
        PublicationFields::addAdministrativeDivisionsEventSubscriber($factory, $builder, [
            'country'=>['field'=>'country','options'=>['options'=>['required' => false]]],
            'admin1'=>['field'=>'admin1','options'=>['options'=>['required' => false]]],
        ]);
        $builder->addEventSubscriber(PublicationFields::getSubscriber($factory, 'link'));

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


