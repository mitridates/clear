<?php

namespace App\Install\Infrastucture\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;

class SetupCountryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('country', countryType::class, [
            'placeholder' => 'Load country',
            'mapped'=>false,
            'required' => true,
        ]);
    }
}


