<?php
namespace App\Form\backend\Map;
use App\Form\EventListener\AddCitationFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapCitationType extends AbstractType implements OneToManyFormTypeInterface
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();

        $builder->addEventSubscriber(new AddCitationFieldSubscriber($factory, array(
            'options'=>['attr'=>['code_id'=>335], 'required'=>true]
        )));

         
        $fields = '599:page,600:comment,10001:position';

        foreach(explode(',', $fields) as $el)
        {
            $field = explode(':', $el);
            $arr = ['attr'=>['code_id'=>$field[0]]];
            $builder->add($field[1], NULL, $arr);
        }
    }


    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
        ));
    }
}



