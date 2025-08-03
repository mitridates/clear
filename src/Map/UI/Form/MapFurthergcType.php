<?php
namespace App\Map\UI\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapFurthergcType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields= [
            ['northlatitude', NumberType::class, ['attr'=>['field_id'=>397]]],
            ['southlatitude', NumberType::class, ['attr'=>['field_id'=>398]]],
            ['eastlongitude', NumberType::class, ['attr'=>['field_id'=>399]]],
            ['westlongitude', NumberType::class, ['attr'=>['field_id'=>400]]],
            ['position', NumberType::class, ['attr'=>['field_id'=>10001]]],
        ];
        foreach ($fields as $field) {
            call_user_func_array([$builder, 'add'], $field);
        }
    }

     
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'attr'=> ['id'=>(new \ReflectionClass($this))->getShortName().rand()]
            ]);
    }

}


