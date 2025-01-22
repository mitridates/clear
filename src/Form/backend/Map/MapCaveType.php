<?php
namespace App\Form\backend\Map;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MapCaveType extends AbstractType implements OneToManyFormTypeInterface
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();

        $caveSubscriber = new AddCaveFieldSubscriber($factory, array(
            'options'=>array(
                'required'=>true,
                'attr'=>array(
                    'code_id'=>601
                )
            )
        ));
        $builder->addEventSubscriber($caveSubscriber);
        $builder->add('position', NULL, ['attr'=>['code_id'=>10001]]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'constraints'=>[
                    new UniqueEntity([
                        'fields' => ['map', 'cave'],
                        'errorPath' => 'cave',
                        'message' => 'This value is already used.',
                    ])
                ]
            ]);
    }
}