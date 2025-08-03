<?php
namespace App\Publication\UI\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationSearchType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
        foreach (PublicationFields::getNamedFields(['publicationyear', 'publicationname']) as $field){
            call_user_func_array([$builder, 'add'], $field);
        }


        PublicationFields::addAdministrativeDivisionsEventSubscriber($factory, $builder, [
            'country'=>['field'=>'country','options'=>['options'=>['required' => false]]],
            'admin1'=>['field'=>'admin1','options'=>['options'=>['required' => false]]],
        ]);

    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}
