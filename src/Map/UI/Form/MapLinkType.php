<?php
namespace App\Map\UI\Form;
use App\Link\UI\Form\EventSubscriber\LinkFieldSubscriber;
use App\Map\UI\Form\Model\ManyToOneFormTypeInterface;
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

        $builder->addEventSubscriber(new LinkFieldSubscriber($factory, array(
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


