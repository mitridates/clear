<?php
namespace App\Map\UI\Form;
use App\Area\UI\Form\EventSubscriber\AreaFieldSubscriber;
use App\Map\Domain\Entity\Map\Mapfurtherpc;
use App\Map\UI\Form\FormTypeFields\MapFields;
use App\Map\UI\Form\Model\ManyToOneFormTypeInterface;
use App\Shared\reflection\EntityReflectionHelper;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MapFurtherpcType extends AbstractType implements ManyToOneFormTypeInterface
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
        $fields= new MapFields();
        $adm=$fields->addAdministrativeDivisionsEventSubscriber($factory, $builder,
            ['field'=>'country'],
            ['field'=>'admin1']
        );
        $area= $fields->getSubscriberData('area');
        $areaSubscriber = new AreaFieldSubscriber($factory, $adm['country']->getCountry(), $adm['admin1']->getAdmin1(), $area[2]);
        $builder->addEventSubscriber($areaSubscriber);
        $builder->add('position', NULL, ['attr'=>['field_id'=>10001]]);

    }

    /**
     * @param Mapfurtherpc $entity
     * @param ExecutionContextInterface $context
     */
    public function constraints(Mapfurtherpc $entity, ExecutionContextInterface $context): void
    {
        if(EntityReflectionHelper::isEmpty($entity,['map', 'position'])){
            $context->buildViolation('form.emptynotallow')
                ->setParameter('code', 100)
                ->addViolation();
        }

    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()],
                'constraints'=>[
                    new Callback([$this, 'constraints']),
                    new UniqueEntity([
                        'fields' => ['area'],
                        'errorPath' => 'area',
                        'message' => 'This value is already used.',
                    ])

                ]
            ]);
    }
}


