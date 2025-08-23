<?php
namespace App\Twig;
use App\SystemParameter\Domain\Entity\SystemParameter;
use App\SystemParameter\Domain\Manager\SystemParameterManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * System parameters in twig
 */
class SystemParameterExtension extends AbstractExtension
{

    private ?SystemParameter $parameters;

    public function __construct(private readonly ?SystemParameterManager $manager)
    {
    }

    public function getSystemParameter(): ?SystemParameter
    {
        if(!$this->parameters) $this->parameters= $this->manager->getSystemParameter();
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('get_system_parameters',array($this, 'getSystemParameter')),
        );
    }
}