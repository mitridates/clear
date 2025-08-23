<?php
namespace App\Map\UI\Form;
use App\Citation\UI\Form\EventSubscriber\CitationFieldSubscriber;
use App\Map\UI\Form\Model\ManyToOneFormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapCitationType extends AbstractType implements ManyToOneFormTypeInterface
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();

        $builder->addEventSubscriber(new CitationFieldSubscriber($factory, array(
            'options'=>['attr'=>['field_id'=>335], 'required'=>true]
        )));

         
        $fields = '599:page,600:comment,10001:position';

        foreach(explode(',', $fields) as $el)
        {
            $field = explode(':', $el);
            $arr = ['attr'=>['field_id'=>$field[0]]];
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



