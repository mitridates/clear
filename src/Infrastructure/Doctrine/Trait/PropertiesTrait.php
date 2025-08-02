<?php

trait PropertiesTrait
{

    private function setNullTraitProperties(ReflectionClass $reflect, ReflectionClass $trait, ?array $exclude=[]):void
    {
        $props = $trait->getProperties();

        foreach ($props as $prop) {
            try {
                $p= $reflect->getProperty($prop->getName());
            }catch(ReflectionException $e){
                return;
            }
            $p->setAccessible(true);
            $p->setValue(null);
        }
    }


    /**
     * @param object $class
     * @param string $tr trait::class
     * @param array|null $exclude
     * @return int % not null in trait
     */
    public function isEmptyTrait(object $class, string $tr, ?array $exclude=[]): int
    {
        $reflect = new ReflectionClass($class);
        $traits = $reflect->getTraits();
        $trait= null;
        $names= [];
        $count=0;
        //$notNull=[];
        foreach ($traits as $k => $t) {
            if($k===$tr){
                $trait= $t;
                break;
            }
        }

        $props = $trait->getProperties();

        foreach ($props as $prop) {
            if(!in_array( $prop->getName(), $exclude)) $names[]= $prop->getName();
        }
        $props = $reflect->getProperties();
        foreach ($props as $prop) {
            if (in_array($prop->getName(), $names)) {
                $prop->setAccessible(true);
                if($prop->getValue($this)!=null){
                    //$notNull[]=$prop->getName();
                    $count++;
                };
            }
        }
//        return $notNull[]
        return $count? number_format(($count/count($names)) * 100) : 0;
    }
}

