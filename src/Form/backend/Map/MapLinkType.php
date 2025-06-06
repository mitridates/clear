<?php
namespace App\Form\backend\Map;
use App\Form\backend\Map\Model\ManyToOneFormTypeInterface;
use App\Form\EventListener\AddLinkFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapLinkType extends AbstractType implements ManyToOneFormTypeInterface
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();

        $builder->addEventSubscriber(new AddLinkFieldSubscriber($factory, array(
            'options'=>['attr'=>['field_id'=>587]]
        )));

        $builder->add('comment', NULL);
        $builder->add('position', NULL, ['attr'=>['field_id'=>10001]]);
    }

        /** @inheritDoc */
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults(array(
                'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
            ));
        }
}


