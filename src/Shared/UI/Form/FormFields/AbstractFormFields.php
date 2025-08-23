<?php

namespace App\Shared\UI\Form\FormFields;

use OutOfRangeException;

abstract class AbstractFormFields implements FormFieldsInterface
{
    /**
     * Form fields array.
     * @var array
     */
    protected array $fields;

    /**
     * Get mapped/unmapped fields ready to add() in FormType
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array ...$fields Field/s to add
     * @return $this
     */
    public function add(...$fields): self
    {
        foreach($fields as $field)
        {
            if(is_string($field[0])){
                $this->set($field[0], $field);
            }else{
                $this->fields[]= $field;
            }
        }
        return $this;
    }

//    public function set($name, array $a):self {
//        $key= $this->getKey($name);
//        $this->fields[$key]= array_merge($this->fields[$key], $a);
//        return $this;
//    }

    public function setFieldOptions($name, array $a):self {
        $key= $this->getKey($name);
        $this->fields[$key][2]= array_merge($this->fields[$key][2], $a);
        return $this;
    }

    public function renameField($name, $newName): self
    {
        $key= $this->getKey($name);
        $this->fields[$key][0]= $newName;
        return $this;
    }


    public function getKey(string $name): int
    {
        foreach ($this->fields as $k=>$item) {
            if ($item[0] === $name) {
                return $k;
            }
        }
        throw new OutOfRangeException(sprintf('Field name for "%s" not found in fields', $name));
    }

    public function getField(string $name): array
    {
        return $this->fields[$this->getKey($name)];
    }
//
//    public function getFieldsNames(): array
//    {
//        $ret= [];
//
//        foreach ($this->fields as $item) {
//            $ret[]=$item[0];
//        }
//        return $ret;
//    }

    /**
     * Get selected fields by name
     * @throws \Exception
     */
    public function getNamedFields(array $names): array
    {
        $ret= $found= [];

        foreach ($this->fields as &$item){
            if(in_array($item[0], $names)){
                 $ret[] = $item;
                $found[]=$item[0];
            }
        }

        if(count($names)!==count($found))
        {
            throw new \Exception(sprintf('Named fields not found: "%s"', implode(', ',
                    array_diff($names, $found))));
        }

        return $ret;
    }
}