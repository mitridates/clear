<?php
namespace App\Shared\JsonApi\ErrorSerializer;

use App\Shared\JsonApi\ErrorSerializer\Exception\ExceptionSerializer;
use App\Shared\JsonApi\ErrorSerializer\FormError\FormErrorIterator;
use App\Shared\JsonApi\ErrorSerializer\FormError\FormErrorSerializer;
use Symfony\Component\Form\FormInterface;

class JsonErrorBag extends AbstractErrorIterator
{
    private array $errors= [];
    private ?ExceptionSerializer $exceptionSerializer= null;
    public function __construct(private readonly string $env='dev')
    {
    }

    public function addException(\Exception $e, ?int $status=null, ?bool $debug=null, ?array $source=[]): JsonErrorBag
    {
        if($this->exceptionSerializer===null) $this->exceptionSerializer = new ExceptionSerializer($this->env);

        $jsonerror= $this->exceptionSerializer->serialize($e, $status, $debug);
        foreach ($source as $k=>$v){
            $jsonerror->setSource($k, $v);
        }
        $jsonerror->setMeta('env', $this->env);
        $this->errors[]= $jsonerror;
        return $this;
    }

    public function addMsg(string $msg, ?string $title='Error', string|int|null $level='danger', ?array $source=[]): JsonErrorBag
    {
        $levels= ['info', 'warning', 'danger'];
        $type = ($level && is_int($level) && isset($levels[$level]))? $levels[$level] : 'danger';
        $jsonError= (new JsonError())
            ->setTitle($title)
            ->setDetail($msg)
            ->setMeta('type','error')
            ->setMeta('timestamp', gmdate('D, d M Y H:i:s T'))
            ->setMeta('env', $this->env)
            ->setMeta('level',$type);

        foreach ($source as $k=>$v){
            $jsonError->setSource($k, $v);
        }
        $this->errors[]= $jsonError;

        return $this;
    }

    public function addJsonError(JsonError $e): JsonErrorBag
    {
        $this->errors[]= $e;
        return $this;
    }

    public function addFormErrors(FormInterface $form): JsonErrorBag
    {
        if(!$form->isSubmitted()){
            $this->addJsonError(JsonErrorMessages::formNotSubmitted($form->getName()));
        }else{
            $err= (new FormErrorIterator($form, new FormErrorSerializer()))->getJsonErrors();
            foreach ($err as $e){
                $this->errors[]= $e;
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getArray(): array
    {
        $arr=[];
        foreach ($this->errors as $error){
            $arr[]= $error->toArray();
        }
        return $arr;
    }

    function getJsonErrors(): array
    {
        return $this->errors;
    }
}