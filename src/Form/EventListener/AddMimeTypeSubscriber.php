<?php
namespace App\Form\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mime\MimeTypes;

/**
 * Set mime types.
 * @package App\EventListener\Form
 */
class AddMimeTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $factory;
    /**
     * @var array
     */
    private $mime;

    public function __construct(FormFactoryInterface $factory, array $mime=[])
    {
        $this->factory = $factory;
        $this->mime= array_replace_recursive(
            array(
                'name'=>'mime',//field name/property if mapped
                'getMethod'=>'getMime',//method if mapped
                'options'=>array(
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-mime jsuggest-select'
                    )
                )
            ),
            $mime
        );
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit',
        );
    }

    /**
     * @param FormInterface $form
     * @param string|null $mime
     */
    private function addMimeForm($form, $mime)
    {
        $options = array_merge(
            array(
                'attr' => array(),
                'value'=> $mime,
                'translation_domain' => 'cavemessages',
                'required' => false,

            ),$this->mime['options'] ?? []
        );

        $form->add($this->mime['name'],null,$options);
    }

    /**
     * @return array
     */
    public function getMime(): array
    {
        return $this->mime;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
//        $data = $event->getData();
        $form= $event->getForm();
//        if (null === $data) return;

//        $mimeType= $this->getMimeType($data->{$this->mime['getMethod']}());
//
//        $data->{$this->mime['setMethod']}($mimeType);
//        $form->add($data);

        $form->add($this->mime['name'], TextType::class, $this->mime['options']);
//        $this->addMimeForm($event->getForm(), $mimeType);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form= $event->getForm();


        if (null !== $data && $data[$this->mime['name']]!==""){
            $mimeType= $this->getMimeType($data[$this->mime['name']] );
            $data[$this->mime['name']]= $mimeType;
            $event->setData($data);
        }

        $form->add($this->mime['name'], null, $this->mime['options']);



//        $this->addMimeForm($event->getForm(), $mimeType);
    }

    private function getMimeType(?string $mime): ?string
    {
        if(!$mime) return null;
        $m= new MimeTypes();
        if(strpos($mime, '/')){
            $arr= $m->getExtensions($mime);
            return count($arr)? $mime : null;
        }else{
            $arr= $m->getMimeTypes($mime);
            return count($arr)? $arr[0] : null;
        }
    }
}