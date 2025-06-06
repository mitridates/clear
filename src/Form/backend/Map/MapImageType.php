<?php
namespace App\Form\backend\Map;
use App\Form\backend\Map\Model\ManyToOneFormTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class MapImageType extends AbstractType implements ManyToOneFormTypeInterface
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $fields= [
            ['mapfile', FileType::class, [
                'mapped' => false,
                'required'=> true,
                'attr'=>[
                    'field_id' => 589,
                    'accept'=> '.jpg, .gif, .png, .pdf, .svc, .tiff, image/*, application/pdf',
                    'data-max-size'=>'8MI'//check bites max size in javascript
                ],
                'multiple'=>false,
                'constraints'=>[
                    new File([
                        'maxSize' => '8M',
                        'binaryFormat'=>true,
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/png',
                            'application/pdf',
                            'image/svg+xml',
                            'image/tiff'
                        ]
                    ])
                ]
            ]
            ],
            ['thumbnail', FileType::class, [
                'mapped' => false,
                'required'=> false,
                'attr'=>[
                    'field_id' => 5891,
                    'accept'=> '.jpg, .gif, .png, image/jpeg, image/gif, image/png',
                    'data-max-size'=>'10000 '//check bites max size in javascript
                ],
                'multiple'=>false,
                'constraints'=>[
                    //Max aspect ratio 3.2:255x170; 4.3:255x191
                    //Min aspect ratio 3.2:180x120; 4.3:180x135
                    new Image([
                        'minWidth' => 180,
                        'maxWidth' => 255,
                        'minHeight' => 120,
                        'maxHeight' => 192,
                    ])
                ]
            ]
            ],
            ['reference', TextareaType::class, ['attr'=>['field_id'=>3072], 'required'=>false]],
            ['comment', null, ['attr'=>['field_id'=>597]]],
            ['citation', null, ['attr'=>['field_id'=>3071]]],
            ['name', null, ['attr'=>['field_id'=>10108]]],
        ];

        foreach ($fields as $field){
            call_user_func_array([$builder, 'add'], $field);
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


