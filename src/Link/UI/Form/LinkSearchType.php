<?php
namespace App\Link\UI\Form;
use Symfony\Component\{Form\AbstractType, Form\FormBuilderInterface, OptionsResolver\OptionsResolver};

class LinkSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
        $fields= new LinkFields();

        foreach ($fields->getNamedFields(['organisationname', 'title', 'authorname', ]) as $field){
            $field[2]['required']= false;
            call_user_func_array([$builder, 'add'], $field);
        }
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'organisation'));
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'author'));
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'mime'));
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>'link_search_form']
        ));
    }
}
