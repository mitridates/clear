<?php

namespace App\Install\Infrastructure\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetupSqlLoaderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $dir = $options['install']['dir'];
        $files = $options['install']['files'];
        $op=[];
        foreach($files as $file){
            $op[$file['name']]=$file['file'];
        }
        $choice= ['files', choiceType::class, ['choices'=> $op, 'label'=>'Choose file to install']];
        call_user_func_array([$builder, 'add'], $choice);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'install'=>[],
        ));
    }
}


