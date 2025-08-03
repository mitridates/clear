<?php
namespace App\Organisation\UI\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationSearchType extends AbstractType
{
    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
        $fields= new OrganisationFields();

        call_user_func_array([$builder, 'add'], $fields->getField('name') );

        $fields->addAdministrativeDivisionsEventSubscriber(
            $factory,
            $builder,
            ['field'=>'country','options'=>['options'=>['required' => false]]],
            ['field'=>'admin1','options'=>['options'=>['required' => false]]],
            ['field'=>'admin2','options'=>['options'=>['required' => false]]],
            ['field'=>'admin3','options'=>['options'=>['required' => false]]]
        );

        foreach ($fields->getNamedFields(['name','type', 'coverage', 'grouping']) as $field){
            $field[2]['required']= false;
            call_user_func_array([$builder, 'add'], $field);
        }

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
