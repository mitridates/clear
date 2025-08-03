<?php
namespace App\UI\Twig;
use App\Shared\Arraypath;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This Bundle parameters twig
 *
 * @author mitridates
 */
class ParametersBagExtension extends AbstractExtension
{
    protected Arraypath $arrayPath;
    protected ParameterBagInterface $bag;

    /**
     * @param ParameterBagInterface $bag Bundle parameters
     */
    public function __construct(ParameterBagInterface $bag)
    {
        $this->arrayPath= new Arraypath($bag->get('cave'));
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return ParametersBagExtension
     */
    public function set(string $key, mixed $value): ParametersBagExtension
    {
        $this->arrayPath->set($key, $value);
        return $this;
    }

    /**
     * @param string $path
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $path, mixed $default = null)
    {
        return $this->arrayPath->get($path, $default);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return array(
            new TwigFunction('setBagParameter',array($this, 'set')),
            new TwigFunction('getBagParameter',array($this, 'get')),

        );
    }
}