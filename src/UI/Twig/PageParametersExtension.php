<?php
namespace App\UI\Twig;
use App\Shared\Arraypath;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Current page parameters
 */
class PageParametersExtension extends AbstractExtension
{
    protected Arraypath $bundle, $router;
    protected ?Arraypath $page;

    public function __construct(protected readonly ParameterBagInterface $params, protected readonly UrlGeneratorInterface $urlGenerator, private readonly  BreadcrumbExtension $bread)
    {
        $this->bundle= new Arraypath($params->get('cave'));
    }

    /**
     * @return Arraypath
     */
    public function getRouter(): Arraypath
    {
        return $this->router;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return PageParametersExtension
     * @throws RuntimeError
     */
    public function set($key, $value): PageParametersExtension
    {
        if(!$this->page){
            throw new RuntimeError('Must initPage() before get any parameter');
        }
        $this->page->set($key, $value);
        return $this;
    }

    /**
     * @param string $path
     * @param mixed|null $default
     * @return mixed
     * @throws RuntimeError
     */
    public function get(string $path, $default = null): mixed
    {
        if(!$this->page){
            throw new RuntimeError('Must initPage() before get any parameter');
        }
        return $this->page->get($path, $default);
    }

    /**
     * @throws RuntimeError
     */
    public function getTitle(string $page, ?string $section= null): string
    {
        if(!$this->page){
            throw new RuntimeError('Must initPage() before get any parameter');
        }
        return ($section ?? $this->page->get('section')).'.'.$page.'.page.title';
    }

    public function issetPage()
    {
        return !!$this->page;
    }


    public function init(string $section, string $name, string $title, string $path= null, ?bool $bread= true): PageParametersExtension
    {
        $default=  $this->bundle->get('section:default', []);
        $params= $this->bundle->get('section:'.$section, []);

        $this->page = new Arraypath(array_merge($default, $params));

        $this->page->set('section', $section)
            ->set('name', $name)
            ->set('title', $title ?? $section.'.'.$name.'.page.title')
            ->set('path', $path)
            ->set('xpath', $section.':'.$name)
            
        ;

        if($bread) $this->bread->addCrumb($title, $path);

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return array(
            
            new TwigFunction('setPage',array($this, 'set')),
            new TwigFunction('getPage',array($this, 'get')),
            new TwigFunction('initPage',array($this, 'init')),
            new TwigFunction('issetPage',array($this, 'issetPage')),

        );
    }
}