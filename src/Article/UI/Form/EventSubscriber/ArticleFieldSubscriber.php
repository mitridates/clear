<?php
namespace App\Article\UI\Form\EventSubscriber;

use App\Article\Domain\Entity\Article;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
  * Set form field on page load or submit if selected value exists.
  * @package App\EventListener\Form
  *
  * @example
  * $article = array(
  *      'name'=>'article',//property/field name
  *      'getMethod'=>'getArticle',//Get method for property if mapped
  *      'options'=>array() //ChoiceType Field options
  *  );
  *
  *
  * $builder->addEventSubscriber(new AddArticleFieldSubscriber( $factory, $article) );
  */
class ArticleFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var array
     */
    private $article;

    public function __construct(FormFactoryInterface $factory, array $article=[])
    {
        $this->factory = $factory;
        $this->article= array_replace_recursive(
            array(
                'name'=>'article',//field name/property if mapped
                'getMethod'=>'getArticle',//method if mapped
                'options'=>array(
                    'placeholder' => 'select.article',
                    'translation_domain' => 'cavemessages',
                    'attr'=>array(
                        'class'=>'js-article jsuggest-select'
                    )
                )
            ),
            $article);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit',
        );
    }

    /**
     * @param FormInterface $form
     * @param string|Article|null $article
     */
    private function addArticleForm($form, $article)
    {
        $options = array_merge(
            array(
                'auto_initialize' => false,
                'attr' => array(
                ),
                'class'=> Article::class,
                'required' => false,
                'translation_domain' => 'cavemessages',
                'placeholder'   => 'select.article',
                'label'=>'Article',
                'choice_label' => 'name',
                'choice_value'=>'id',
                'query_builder' => function (EntityRepository $repository) use ($article) {
                    return $repository->createQueryBuilder('o')
                            ->where('o.id = :article')
                            ->setParameter('article', $article);
                }
            ),$this->article['options']
        );

       $form->add($this->factory->createNamed(
           $this->article['name'] ,
           EntityType::class,
           $article,
           $options
       ));
    }

    /**
     * @return array
     */
    public function getArticle(): array
    {
        return $this->article;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addArticleForm($event->getForm(), $data->{$this->article['getMethod']}() ?? null);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (null === $data) return;

        $this->addArticleForm($event->getForm(), $data[$this->article['name']] ?? null);
    }

}