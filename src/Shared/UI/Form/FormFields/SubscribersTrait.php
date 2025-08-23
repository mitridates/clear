<?php

namespace App\Shared\UI\Form\FormFields;

use Symfony\Component\Form\FormFactoryInterface;

Trait SubscribersTrait
{
    public array $subscribers;
    public  function getSubscribers(): array
    {
        return $this->subscribers;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getSubscriberData(string $name):array
    {
            $sub= $this->getSubscribers();
            foreach ($sub as $s){
                if($s[0]===$name) return $s;
            }
        throw new \OutOfRangeException(sprintf('Subscriber %s not found', $name));
    }

    public  function getSubscriber(FormFactoryInterface $factory, string|array $field, ?array $options=[])
    {
        if(is_string($field)){
            $field= $this->getSubscriberData($field);
        }

        $o= array_replace_recursive($field[2]??[], $options);
        return new $field[1]($factory, $field[2]??[]);
    }
}