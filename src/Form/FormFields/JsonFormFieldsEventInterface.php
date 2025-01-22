<?php
namespace App\Form\FormFields;

use Symfony\Component\Form\Test\FormInterface;

interface JsonFormFieldsEventInterface {

    
    /**
     * set/unset/modify/validate json $data before save entity
     * @param FormEvent $event
     * @return void
     */
    public static function setPostSubmitJsonData(FormInterface $form, array &$data): void;

}
