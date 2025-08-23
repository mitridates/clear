<?php
namespace App\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Simple array to render breadcrumb in twig
 *
 * @author mitridates
 */
class BreadcrumbExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $breadcrumb= [];

    /**
     * @return array
     */
    public function getBreadcrumb(): array
    {
        return $this->breadcrumb;
    }

    public function hasCrumb(string $text, $path): bool
    {
        foreach ($this->breadcrumb as $crumb){
            if($crumb['text']===$text && $crumb['path']===$path) return true;
        }
        return false;
    }

    /**
     * Prepend Home crumb
     * @param $title
     * @param $path
     * @param $titleAttr
     * @return BreadcrumbExtension
     */
    public function prepend($title, $path= false , $titleAttr= false): BreadcrumbExtension
    {
        array_unshift($this->breadcrumb, [
            'path'=>$path,
            'text'=> $title,
            'title'=> $titleAttr
        ]);
        return $this;
    }

    /**
     * Append crumb
     * @param $title
     * @param $path
     * @param $titleAttr
     * @return BreadcrumbExtension
     */
    public function addCrumb($title, $path= false , $titleAttr= false): BreadcrumbExtension
    {
        $this->breadcrumb[] = [
            'path' => $path,
            'text' => $title,
            'title' => $titleAttr
        ];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return array(
            new TwigFunction('get_breadcrumb',array($this, 'getBreadcrumb')),
            new TwigFunction('addCrumb',array($this, 'addCrumb')),
            new TwigFunction('prependCrumb',array($this, 'prepend'))
        );
    }

}