<?php
namespace App\Form\backend\Citation\CommonFields;

use App\Entity\Citation\Citation;
use App\Form\FormFields\AbstractFormFields;
use App\Form\FormFields\JsonFormFieldsEventInterface;
use DateTime;
use PharIo\Version\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;

class JsonDateFields extends AbstractFormFields implements JsonFormFieldsEventInterface
{
    public function __construct(FormBuilderInterface $builder)
    {
        $form= $builder->getForm();
        /** @var Citation $citation */
        $citation = $form->getData();
        $json= json_decode($citation->getJsondata(), true);

        $this->fields= [
            ['year', IntegerType::class, [
                'label'=>'Year',
                'required'=>false,
                'mapped'=>false,
                'constraints' => [
                    new Length(['max'=>4]),
                ],
                'attr'=>[
                    'class'=>'js-year',
                    'placeholder'=> 'Year',
                ]

            ]],
            ['month', ChoiceType::class, [
//                'choices'=>self::getMonthWithSeasonChoices(),
                'label'=>'Month',
                'required'=>false,
                'mapped'=>false,
                'placeholder'=>'No month',
                'attr'=>[
                    'class'=>'js-month',
                ]

            ]],
            ['day', ChoiceType::class, [
                'label'=>'Day',
                'required'=>false,
//                'choices'=>self::getDaysInMonthYear($json['month']??null,$json['year']??null),
                'mapped'=>false,
                'placeholder'=>'No day',
                'attr'=>[
                    'class'=>'js-day',
                ],
            ]],
        ];

    }

    public static function getMonthChoices():array
    {
        $ret=[];
        $locale = \Locale::getDefault();

        $dateFormatter = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::LONG, // date type
            \IntlDateFormatter::NONE  // time type
        );
        $dateFormatter->setPattern('LLLL'); // full month name with NO DECLENSION ;-)
        $months_locale = [];
        for ($month_number = 1; $month_number <= 12; ++$month_number) {
            $months_locale[] = $monty_name= $dateFormatter->format(
            // 'n' => month number with no leading zeros
                DateTime::createFromFormat('n', (string)$month_number)
            );
            $ret[$monty_name]= $month_number;
        }
        return $ret;
    }
    
    public static function getDaysInMonthYear($m, $y):array
    {
        $ret= [];
        if(!$m || !$y || !is_int($m)) return $ret;
        try {
            $n= cal_days_in_month( 0, $m, $y);
            for($i=1; $i<=$n; $i++){
                $ret[$i]=$i;
            }
        }catch (Exception $e){}
        return $ret;
    }
    
    public static function getMonthWithSeasonChoices(): array
    {
        $ret= self::getMonthChoices();
        $season= [ 'Spring', 'Summer', 'Fall', 'Winter'];

        foreach($season as $s){
            $ret['Season'][$s]= strtolower($s);
        }
        return $ret;
    }

    /**
     * @inheritDoc
     */
    public static function setPostSubmitJsonData(FormInterface $form, array &$data): void
    {
        foreach (['year', 'month', 'day'] as $d)
        {
            $v= $form->get($d)->getData();
            if($v){
                  $data[$d]=$v;
            }else{
                 unset($data[$d]);
            }
        }
    }

}