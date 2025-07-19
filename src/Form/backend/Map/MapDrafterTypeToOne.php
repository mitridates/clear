<?php
namespace App\Form\backend\Map;
use App\Entity\Map\Mapdrafter;
use App\Form\backend\Map\FormTypeFields\MapControllerFields;
use App\Form\backend\Map\FormTypeFields\MapFields;
use App\Form\backend\Map\Model\ManyToOneFormTypeInterface;
use App\Form\EventListener\AddPersonFieldSubscriber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MapDrafterTypeToOne extends AbstractType implements ManyToOneFormTypeInterface
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
//        $fields= new MapFields();
//        $builder->addEventSubscriber($fields->getSubscriber($factory,'drafterid'));
        $builder->addEventSubscriber(new AddPersonFieldSubscriber($factory, array(
            'name'=>'drafterid',
            'options'=>['attr'=>['field_id'=>587]]
        )));

        $builder->add('drafter', NULL, ['attr'=>['field_id'=>585]])
                ->add('position', NULL, ['attr'=>['field_id'=>10001]]);
    }

    /**
     * Constraints
     * @param Mapdrafter $entity
     * @param ExecutionContextInterface $context
     */
    public function constraints(Mapdrafter $entity, ExecutionContextInterface $context): void
    {
        if(!is_null($entity->getDrafterid()) && !is_null($entity->getDrafter())){
            $context->buildViolation('form.multiplenotallow')
                ->addViolation();
        }

        if(is_null($entity->getDrafterid()) && is_null($entity->getDrafter())){
            $context->buildViolation('form.emptynotallow')
                ->addViolation();
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()],
                'constraints'=>[
                    new Callback([$this, 'constraints']),
                    new UniqueEntity([
                        'fields' => ['map', 'drafterid'],
                        'errorPath' => 'drafterid',
                        'message' => 'This value is already used.',
                    ]),
                    new UniqueEntity([
                        'fields' => ['map', 'drafter'],
                        'errorPath' => 'drafter',
                        'message' => 'This value is already used.',
                    ])
                ]
            ]);
    }
}