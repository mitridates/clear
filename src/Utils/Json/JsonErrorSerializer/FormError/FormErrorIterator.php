<?php
namespace App\Utils\Json\JsonErrorSerializer\FormError;

use App\Utils\Json\JsonErrorSerializer\AbstractErrorIterator;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class FormErrorIterator extends AbstractErrorIterator
{

    private array $errors= [];

    public function __construct(private readonly FormInterface $form, private readonly FormErrorSerializer $serializer)
    {
        $this->iterate();
    }

    private function iterate(): void
    {
        foreach ($this->form->getErrors() as $error)//GLOBAL FORM ERRORS
        {
            $this->errors[]= $this->serializer->serializeGlobal($error, $this->form);
        }
        /**
         * CHILD(FIELD) ERRORS
         */
        foreach ($this->form->getIterator() as $key => $child)//FORM FIELD ERRORS
        {
            /**
             * @var FormError $error Field errors
             */
            foreach ($child->getErrors() as $error) {
                $this->errors[]= $this->serializer->serializeChild($error, $key, $child, $this->form);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getArray(): array
    {
        $err= [];
        foreach ($this->errors as $e)//GLOBAL FORM ERRORS
        {
            $err[]= $e->toArray();
        }
        return $err;
    }

    /**
     * @inheritDoc
     */
    function getJsonErrors(): array
    {
        return $this->errors;
    }
}