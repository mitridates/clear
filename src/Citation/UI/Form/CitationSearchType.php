<?php
namespace App\Citation\UI\Form;
use App\Citation\UI\Form\CommonFields\CitationEntityFields;
use Symfony\Component\{Form\AbstractType,
    Form\Extension\Core\Type\TextType,
    Form\FormBuilderInterface,
    OptionsResolver\OptionsResolver};

class CitationSearchType extends AbstractType
{
    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $cf= new CitationEntityFields($builder);

        foreach ($cf->getNamedFields(['title', 'subtitle' ,'contributor', 'type' ]) as $field){
            $field[2]['required']= false;
            if($field[0]==='contributor'){
                $field[1]= TextType::class;
                $field[2]['attr']= [];
            }
            call_user_func_array([$builder, 'add'], $field);
        }
       
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'attr'=> ['id'=>'CitationSearchForm']
        ));
    }
}
