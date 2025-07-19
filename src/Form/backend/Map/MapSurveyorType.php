<?php
namespace App\Form\backend\Map;
use App\Entity\Map\Mapsurveyor;
use App\Form\backend\Map\FormTypeFields\MapControllerFields;
use App\Form\backend\Map\Model\OneToOneFormTypeInterface;
use App\Form\EventListener\AddPersonFieldSubscriber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MapSurveyorType extends AbstractType implements OneToOneFormTypeInterface
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();

        $fields= new MapControllerFields();
        $builder->addEventSubscriber(new AddPersonFieldSubscriber($factory, array(
            'name'=>'surveyorid',
            'options'=>['attr'=>['field_id'=>586]]
        )));

        $builder->addEventSubscriber(new AddPersonFieldSubscriber($factory, array(
            'name'=>'surveyorid',
            'options'=>['attr'=>['field_id'=>586]]
        )));

        $builder->add('surveyor', NULL, ['attr'=>['field_id'=>584]])
                ->add('position', NULL, ['attr'=>['field_id'=>10001]]);
    }

    /**
     * Constraints
     * @param Mapsurveyor $entity
     * @param ExecutionContextInterface $context
     */
    public function constraints(Mapsurveyor $entity, ExecutionContextInterface $context): void
    {
        if(!is_null($entity->getSurveyorid()) && !is_null($entity->getSurveyor())){
            $context->buildViolation('form.multiplenotallow')
                ->addViolation();
        }

        if(is_null($entity->getSurveyorid()) && is_null($entity->getSurveyor())){
            $context->buildViolation('form.emptynotallow')
                ->addViolation();
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()],
                'constraints'=>[
                    new Callback([$this, 'constraints']),
                    new UniqueEntity([
                        'fields' => ['map', 'surveyorid'],
                        'errorPath' => 'surveyorid',
                        'message' => 'This value is already used.',
                    ]),
                    new UniqueEntity([
                        'fields' => ['map', 'surveyor'],
                        'errorPath' => 'surveyor',
                        'message' => 'This value is already used.',
                    ])
                ]
            ]);
    }
}