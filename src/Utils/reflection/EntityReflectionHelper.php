<?php
namespace App\Utils\reflection;

use Doctrine\Common\Util\ClassUtils;
use PHPUnit\Exception;
use ReflectionClass;
use ReflectionException;


class EntityReflectionHelper
{

    /**
     * @throws ReflectionException
     */
    public static function setNullTraitproperties(&$class, string $traitClass):void
    {
        $reflect= new ReflectionClass($class);

        $trait= $reflect->getTraits()[$traitClass];
        $props = $trait->getProperties();
        foreach ( $props as $prop) {
            $p= $reflect->getProperty($prop->getName());

            $fn='set'.ucfirst(strtolower($prop->getName()));
            if(!method_exists($class, $fn)) continue;//MUST EXISTS get method
            $class->$fn(null);
        }
    }

    public static function setNullProperties(mixed &$class, array $properties):void
    {
        foreach ($properties as $prop) {
            self::setPrivateProperty($class, $prop, null);
        }
    }

    public static function setPrivateProperty(mixed $class, string $prop, mixed $value):void
    {
        try {
            $reflection = new \ReflectionProperty($class, $prop);
            $reflection->setAccessible(true);
            $reflection->setValue($class, $value);
        } catch (\Exception $e) {
            return;
        }
    }

    public static function isNull($class, string|array $names): bool
    {
        $names= is_array($names)? $names : array($names);
        foreach ($names as $name)
        {
            $reflection = new \ReflectionProperty($class, $name);
            $reflection->setAccessible(true);
            if($reflection->getValue($class)!==null) return false;
        }
        return true;
    }

    public static function isEmpty($class, ?array $exclude=[], $callback=null): bool
    {

        if(!(self::percentageOfNotNullProperties($class, $exclude)===0)) return false;

        if(!$callback) return true;

        if(is_array($callback)){
            foreach ($callback as $fn) {
                if(!$fn($class)) return false;
            }
        }else if(is_callable($callback)) {
            return $callback($class);
        }
        return true;
    }

    public static function isEmptyTrait($class, string $traitClass, ?array $exclude=[], $callback=null): bool
    {

        $empty=  self::percentageOfNotNullTraitProperties($class,  $traitClass, $exclude)===0;
        if(!$callback && $empty) return true;

        if(is_array($callback)){
            foreach ($callback as $fn) {
                if(!$fn($class)) return false;
            }
        }else if(is_callable($callback)) {
            return $callback($class);
        }
        return $empty;
    }

    public static function percentageOfNotNullProperties($class, ?array $exclude=[]):int
    {
        try {
            $reflect= new ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return 0;
        }

        $count=0;
        $rps = $reflect->getProperties();
        $length=0;

        foreach ($rps as $rp) {
            if (in_array($rp->getName(), $exclude)) continue;
            $length++;
            $p= $reflect->getProperty($rp->getName());

            //para probar...
//            if(!$p->isInitialized($class)){
//                try {
//                    $class->{'get'.ucfirst($rp->getName())}();
//                }catch (\Exception $e){}
//
//            }
            if($p->getValue($class)!=null){
                $count++;
            };
        }
        return $count? number_format(($count/$length) * 100) : 0;
    }

    public static function percentageOfNotNullTraitProperties($class, string $traitClass, ?array $exclude=[]): int|string|null
    {
        try {
            $reflect= new ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return null;
        }

        $trait= $reflect->getTraits()[$traitClass];
        $count=0;
        $rps = $trait->getProperties();
        $length=0;

        foreach ($rps as $rp)
        {
            if (in_array($rp->getName(), $exclude)) continue;
            $length++;
            $p= $reflect->getProperty($rp->getName());

            if($p->getValue($class)!=null){
                $count++;
            };
        }

        return $count? number_format(($count/$length) * 100) : 0;
    }

    /**
     * @param mixed $model
     * @param ?array $fields
     * @return array
     */
    public static function serializeClassProperties(mixed $model, ?array $fields = []): array
    {
        try {
            $rprop= (new \ReflectionClass(ClassUtils::getClass($model)))->getProperties();
        } catch (\ReflectionException $e) {
            return [];
        }
        $props= [];

        foreach ( $rprop as $reflectionProperty){
            $name = $reflectionProperty->getName();
            if($name==='id') continue;//id is not an attribute

            if($fields && !in_array($name, $fields)) continue;//if isset fields, MUST EXISTS $name key

            ///MAL. NOS DA UN PROXY. HAY QUE UTILIZAR GETx() PARA INICIALIZARLO. HAY OTRA MANERA?
//          $data= $reflectionProperty->getValue($model);

            $fn='get'.ucfirst(strtolower($name));
            if(!method_exists($model, $fn)) continue;//MUST EXISTS get method
            $data = $model->$fn();
            //end

            if($data instanceof \DateTime){
                $props[$name]= $data->format('Y-m-d H:i:s');
            }else{
                $props[$name]= $data;
            }
        }
        return $props;
    }

    /**
     * @param mixed $target
     * @param mixed $source
     * @param array|null $exclude
     * @return mixed
     * @throws ReflectionException
     */
    public static function assign(mixed $target, mixed $source, ?array $exclude=[]): mixed
    {
//        try {
            $rtarget= new ReflectionClass($target);
            $rsource= new ReflectionClass($source);
//        } catch (\ReflectionException $e) {
//            return $target;
//        }

        foreach ($rsource->getProperties() as $rpS) {
            if (in_array($rpS->getName(), $exclude)) continue;

            if ($rtarget->hasProperty($rpS->getName()))
            {
                $p = $rtarget->getProperty($rpS->getName());
                $p->setValue($target, $rpS->getValue($source));
            }
        }
        return $target;
    }
}


