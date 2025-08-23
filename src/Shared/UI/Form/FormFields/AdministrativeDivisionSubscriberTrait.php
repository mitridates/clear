<?php

namespace App\Shared\UI\Form\FormFields;

use App\Geonames\UI\Form\EventSubscriber\Admin1FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\Admin2FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\Admin3FieldSubscriber;
use App\Geonames\UI\Form\EventSubscriber\CountryFieldSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

Trait AdministrativeDivisionSubscriberTrait
{
    use SubscribersTrait;
    abstract function getFields(): array;

    /**
     * @param FormFactoryInterface $factory
     * @param string|array $field
     * @param array|null $options
     * @return CountryFieldSubscriber
     */
    public function getCountry(FormFactoryInterface $factory, string|array $field, ?array $options=[]): CountryFieldSubscriber
    {
        if(is_string($field)) $field= $this->getSubscriberData($field);
        $o= array_replace_recursive($field[2]??[], $options);
        return new $field[1]($factory, $o);
    }

    /**
     * @param FormFactoryInterface $factory
     * @param array $country
     * @param string|array $field
     * @param array|null $options
     * @return Admin1FieldSubscriber
     */
    public function getAdmin1(FormFactoryInterface $factory, array $country, string|array $field, ?array $options=[]): Admin1FieldSubscriber
    {
        if(is_string($field)) $field= $this->getSubscriberData($field);
        $o= array_replace_recursive($field[2]??[], $options);
        return new $field[1]($factory, $country, $o);
    }

    /**
     * @param FormFactoryInterface $factory
     * @param array $country
     * @param array $admin1
     * @param string|array $field
     * @param array|null $options
     * @return Admin2FieldSubscriber
     */
    public function getAdmin2(FormFactoryInterface $factory, array $country, array $admin1, string|array $field, ?array $options=[]): Admin2FieldSubscriber
    {
        if(is_string($field)) $field= static::getSubscriberData($field);
        $o= array_replace_recursive($field[2]??[], $options);
        return new $field[1]($factory, $country, $admin1, $o);
    }

    /**
     * @param FormFactoryInterface $factory
     * @param array $admin2
     * @param string|array $field
     * @param array|null $options
     * @return Admin3FieldSubscriber
     */
    public function getAdmin3(FormFactoryInterface $factory, array $admin2, string|array $field, ?array $options=[]): Admin3FieldSubscriber
    {
        if(is_string($field)) $field= $this->getSubscriberData($field);
        $o= array_replace_recursive($field[2]??[], $options);
        return new $field[1]($factory, $admin2, $o);
    }

    /**
     * @param FormFactoryInterface $factory
     * @param FormBuilderInterface $builder
     * @param array $co Country
     * @param array $a1 Admin1
     * @param array|null $a2 Admin2
     * @param array|null $a3 Admin3
     * @return (EventSubscriberInterface|null)[] ['country', 'admin1', 'admin2', 'admin3']
     */
    public function addAdministrativeDivisionsEventSubscriber(FormFactoryInterface $factory,
                                                              FormBuilderInterface $builder,
                                                              array $co,
                                                              array $a1,
                                                              ?array $a2=null,
                                                              ?array $a3=null
                                                              ): array
    {

      $def= ['factory'=>$factory];
  //Todo hay que mejorar esto...
        $current= array_merge($def, $co);
        $ret['country']= $country= call_user_func_array('self::getCountry', $current);
        $builder->addEventSubscriber($country);

        $current= array_merge($def, ['country'=>$country->getCountry()], $a1);
        $ret['admin1']= $admin1= call_user_func_array('self::getAdmin1', $current);
        $builder->addEventSubscriber($admin1);

        if($a2){
            $current= array_merge($def, ['country'=>$country->getCountry(), 'admin1'=>$admin1->getAdmin1()], $a2);
            $ret['admin2']= $admin2= call_user_func_array('self::getAdmin2' , $current);
            $builder->addEventSubscriber($admin2);
        }else{
            $ret['admin2']=null;
        }

        if($a3){
            $current= array_merge($def, ['admin2'=>$admin2->getAdmin2()], $a3);
            $ret['admin3']= $admin3= call_user_func_array('self::getAdmin3', $current);
            $builder->addEventSubscriber($admin3);
        }else{
            $ret['admin3']=null;
        }
//
//        if(isset($a1){
//
//            $current= array_merge($def, ['country'=>$country->getCountry()], $adm['admin1']);
//            $a1= call_user_func_array('self::getAdmin1', $current);
//            $builder->addEventSubscriber($a1);
//            $ret['admin1']=$a1;
//            if(isset($adm['admin2'])){
//                $current= array_merge($def, ['country'=>$country->getCountry(), 'admin1'=>$a1->getAdmin1()], $adm['admin2']);
//                $a2= call_user_func_array('self::getAdmin2' , $current);
//                $builder->addEventSubscriber($a2);
//                $ret['admin2']=$a2;
//
//                if(isset($adm['admin3'])){
//                    $current= array_merge($def, ['admin2'=>$a2->getAdmin2()], $adm['admin3']);
//                    $a3= call_user_func_array('self::getAdmin3', $current);
//                    $builder->addEventSubscriber($a3);
//                    $ret['admin3']=$a3;
//
//                }
//            }
//        }
        return $ret;
    }

    public function addAdAreaEventSubscriber(
        FormFactoryInterface $factory,
        FormBuilderInterface $builder,
        array|string $field, ?array $country, ?array $admin1 ,?array $options = [])
    {
        if(!is_array($field)) $field= $this->getSubscriberData($field);
        $builder->addEventSubscriber(new $field[1]($factory, $country, $admin1, array_replace_recursive($field[2]??[], $options)));

    }
}