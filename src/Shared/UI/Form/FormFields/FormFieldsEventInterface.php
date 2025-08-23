<?php
namespace App\Shared\UI\Form\FormFields;

use Symfony\Component\Form\FormEvent;

interface FormFieldsEventInterface {

    
    /**
     * Event to save unmapped fields to json, modify/validate values...
     * @param FormEvent $event
     * @return void
     */
    public  static function setPostSubmitData(FormEvent $event): void;

}
