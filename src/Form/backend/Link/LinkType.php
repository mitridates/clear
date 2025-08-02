<?php
namespace App\Form\backend\Link;
use App\Domain\Link\Entity\Link;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class LinkType extends AbstractType
{

    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factory = $builder->getFormFactory();
        $fields= new LinkFields();

        foreach ($fields->getFields() as $field){
            call_user_func_array([$builder, 'add'], $field);
        }

        $builder->addEventSubscriber($fields->getSubscriber($factory, 'organisation'));
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'author'));
        $builder->addEventSubscriber($fields->getSubscriber($factory, 'mime'));

    }


    private function checkMime($mime): array
    {
        $m= new MimeTypes();
        $modified=false;
        if(strpos($mime, '/')){
            $arr= $m->getExtensions($mime);
            $guessed= count($arr)? $mime : false;
        }else{
            $arr= $m->getMimeTypes($mime);
            $guessed= count($arr)? $arr[0] : false;
            if($guessed) $modified=true;
        }
        return [$guessed, $modified];
    }


    public function constraints(Link $entity, ExecutionContextInterface $context): void
    {

        if($entity->getMime())
        {
            list($guessed, $modified)= $this->checkMime($entity->getMime());
            if(!$guessed)
            {
                $context->buildViolation('This value is not valid.')
                    ->atPath('mime')
                    ->setInvalidValue($entity->getMime())
                    ->setCode(100)
                    ->addViolation();
            }
        }
    }

    /** @inheritDoc */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>'linkForm'],
            'constraints'=>[
                new Callback([$this, 'constraints'])
            ]
        ));
    }
}