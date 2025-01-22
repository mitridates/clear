<?php


namespace App\Form\FormFields;

use Symfony\Component\Form\FormBuilderInterface;

interface FormFieldsInterface {

    /**
     * Get all fields.
     *
     * @return array
     */
    public function getFields(): array;
    
}
