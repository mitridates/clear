<?php
namespace App\Form\EventListener;
use App\Fielddefinition\Domain\Entity\Fieldvaluecode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Remove lengthcategory & depthcategory and set values on submit
 * @package App\EventListener\Form
 */
class AddDimensionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SUBMIT     => 'preSubmit'
        );
    }

    /**
     * @param int $depth
     * @return Fieldvaluecode
     */
    private function getDepthCategory(int $depth)
    {
        $vc= new Fieldvaluecode();
        $vc->setCode('527');
        switch (true){
            case $depth<50: return  $vc->setValue('527.1');
            case $depth >= 50 & $depth <= 99: return  $vc->setValue('527.2');
            case $depth >= 100 & $depth <= 199: return  $vc->setValue('527.3');
            case $depth >= 200 & $depth <= 499: return  $vc->setValue('527.4');
            case $depth >= 500 & $depth <= 749: return  $vc->setValue('527.5');
            case $depth >= 750 & $depth <= 999: return  $vc->setValue('527.6');
            case $depth >= 1000 & $depth <= 1249: return  $vc->setValue('527.7');
            case $depth >= 1250 & $depth <= 1499: return  $vc->setValue('527.8');
            case $depth > 1499 : return  $vc->setValue('527.9');
            default: return  $vc->setValue('527.0');//unknown
        }
    }

    /**
     * @param ?int $length
     * @return Fieldvaluecode
     */
    private function getLengthCategory(?int $length)
    {
        $vc= new Fieldvaluecode();
        $vc->setCode('297');
        switch (true){
            case $length<50: return  $vc->setValue('297.1');
            case $length >= 50 & $length <= 499: return  $vc->setValue('297.2');
            case $length >= 500 & $length <= 4999: return  $vc->setValue('297.3');
            case $length >= 5000 & $length <= 9999: return  $vc->setValue('297.4');
            case $length >= 10000 & $length <= 24999: return  $vc->setValue('297.5');
            case $length >= 25000 & $length <= 49999: return  $vc->setValue('297.6');
            case $length >= 50000 & $length <= 99999: return  $vc->setValue('297.7');
            case $length >= 100000 & $length <= 499999: return  $vc->setValue('297.8');
            case $length > 499999 : return  $vc->setValue('297.9');
            default:  return $vc->setValue('297.0');//unknown
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;
        $form = $event->getForm();

        if(isset($data['verticalextent']))
        {
            $f= $this->getDepthCategory((int)$data['verticalextent']);
            $form->add('depthcategory', EntityType::class, [
                'class'=>Fieldvaluecode::class
            ]);
            $data['depthcategory']=$f->getValue();
            $event->setData($data);
        }

        if(isset($data['length']))
        {
            $f= $this->getLengthCategory((int)$data['length']);
            $form->add('lengthcategory', EntityType::class, [
                'class'=>Fieldvaluecode::class
            ]);
            $data['lengthcategory']=$f->getValue();
            $event->setData($data);
        }
    }
}