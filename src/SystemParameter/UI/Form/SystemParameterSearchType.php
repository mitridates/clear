<?php
namespace App\SystemParameter\UI\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SystemParameterSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $factory = $builder->getFormFactory();
        $fields= new SystemParameterFields($options);

        foreach ($fields->getFields() as $field)
        {
            if(!in_array($field[0], ['name','language'])) continue;

            $options= &$field[2];
            $options = array_replace_recursive($options, ['required' => false]);
            call_user_func_array([$builder, 'add'], $field);
        }
        foreach ($fields->getSubscribers() as $s)
        {
            if(!in_array($s[0], ['country','getOrganisationdbm'])) continue;

            $arg2= &$s[2];
            $arg2 = array_replace_recursive($arg2, ['options'=>['required' => false]]);
            $builder->addEventSubscriber(new $s[1]($factory, $s[2]));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'locales'=>[],
            'attr'=> ['id'=>'organisation_search_form'],
            'validation_groups' => false,
        ]);
    }
}
