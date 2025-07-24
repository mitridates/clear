<?php
namespace App\Shared\JsonApi\ErrorSerializer\FormError;

use App\Shared\JsonApi\ErrorSerializer\JsonError;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class FormErrorSerializer
{

    /**
     * @inheritDoc
     */
    public function serializeGlobal(FormError $error, FormInterface $form): JsonError
    {
       $msg= $error->getMessage();
        $je= new JsonError();

        return $je
            ->setId('UID_GLOBAL_FORM_ERROR_'.$je->generateId())//a unique identifier for this particular occurrence of the problem.
            ->setCode('formGlobalError')//an application-specific error code, expressed as a string value.
            ->setStatus(400)
            ->setSource('pointer', '/'.implode('/', [$form->getName()]))
            ->setTitle('Invalid form')
            ->setDetail($msg)
            ->setMeta('form', [
                'name' => $form->getName(),
                'id' => $form->getConfig()->getOptions()['attr']['id'] ?? null,
                'cause'=>$error->getCause(),
                'origin'=>$error->getOrigin(),
                'timestamp'=> gmdate('D, Y-m-d H:i:s T')
            ])
            ->setMeta('test', $form->getErrors())
            ->setMeta('message', [
                'message' => $error->getMessage(),
                'parameters'=>$error->getMessageParameters(),
                'template' => $error->getMessageTemplate(),
            ])
            ->setMeta('type','formGlobal')
            ;
    }

    /**
     * @inheritDoc
     */
    public function serializeChild(FormError $error, string $key, FormInterface $child, FormInterface $form): JsonError
    {
        $view = $form->createView();
        $je= new JsonError();
        $forId= $form->getConfig()->getOptions()['attr']['id'] ?? null;
        return $je
            ->setId()// a unique identifier for this particular occurrence of the problem.
            ->setCode('formChildrenError')//an application-specific error code, expressed as a string value.
            ->setStatus(400)
            ->setSource('pointer', '/'.implode('/', [$form->getName(), $view->offsetGet($key)->vars['id'] ]))
            ->setTitle('Invalid form attribute')
            ->setDetail($error->getMessage())
            ->setMeta('form', [
                'name' => $form->getName(),
                'id' => $form->getConfig()->getOptions()['attr']['id'] ?? null,
                'timestamp', gmdate('D, d M Y H:i:s T')
//                'otro'=> $form->getConfig()->getAttributes(),
//                'cause'=>$error->getCause(),
//                'origin'=>$error->getOrigin()
            ])
            ->setMeta('trans', [
                'message' => $error->getMessage(),
                'template' => $error->getMessageTemplate(),
//                'parameters' => $error->getMessageParameters()
            ])
            ->setMeta('id',$view->offsetGet($key)->vars['id'] )
            ->setMeta('type','formChildren')
            ->setMeta('name',$view->offsetGet($key)->vars['name'] )
            ->setMeta('label',$view->offsetGet($key)->vars['label'] )
            //->setMeta('attr',$form->get($key)->getConfig()->getOptions()['attr'])
//                    ->setMeta('view',array_keys($view->offsetGet($key)->vars))//more
//                    ->setMeta('formType',array_keys($form->get($key)->getConfig()->getOptions()))//more
            ;
    }
}