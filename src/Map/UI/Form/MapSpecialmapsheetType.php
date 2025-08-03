<?php
namespace App\Map\UI\Form;
use App\Map\UI\Form\Model\ManyToOneFormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapSpecialmapsheetType extends AbstractType implements ManyToOneFormTypeInterface
{
    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', null, ['attr'=>['field_id'=>558], 'required'=>true]);
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]));
    }
}


