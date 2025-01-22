<?php
namespace App\Form\backend\Map;
use App\Form\backend\Map\FormTypeFields\MapControllerFields;
use App\Form\backend\Map\FormTypeFields\MapOneToOneFields;
use App\Form\backend\Map\Model\OneToOneFormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;

class MapControllerType extends AbstractType implements OneToOneFormTypeInterface
{
    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
        $fields= new MapControllerFields();

        foreach (MapOneToOneFields::CONTROLLER_FIELDS as $field){
            call_user_func_array([$builder, 'add'], $field);
        }

        foreach ($fields->getSubscribers() as $subscriber){
            $builder->addEventSubscriber($fields->getSubscriber($factory, $subscriber));
        }
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()],
            'constraints'=>[
                new Callback([new MapControllerFields(), 'constraints'])
            ]
        ));
    }
}


