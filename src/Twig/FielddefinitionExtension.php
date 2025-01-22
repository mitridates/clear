<?php
namespace App\Twig;
use App\Manager\FieldDefinitionManager;
use App\Services\Cache\FilesCache\FieldDefinitionCache;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Fielddefinition in twig
 *
 * @author mitridates
 */
class FielddefinitionExtension extends AbstractExtension
{

    public function __construct(
        private readonly FieldDefinitionCache $cache,
        private readonly FieldDefinitionManager $fdManager,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {}

    /**
     * Get Fielddefinition by code
     * @param int $code
     * @param null|string $locale
     * @param bool $abbr Get Abbreviated
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function getFieldDefinitionName(int $code, ?string $locale, bool $abbr=false): ?string
    {
        $fd_json = $this->cache->getFieldDefinition($code);
        if(!$fd_json){
            $fd = $this->fdManager->repo->find($code);
            if(!$fd) return null;
            $fd_json= $this->cache->warmup($this->fdManager, $fd, $this->urlGenerator)->getFieldDefinition($code, $locale);
        }
        $attr= $fd_json['data']['attributes'];
        return ($abbr && $attr['abbreviation'])? $attr['abbreviation']:$attr['name'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return array(
            new TwigFunction('get_fielddefinition_name',array($this, 'getFieldDefinitionName')),
//            new TwigFunction('get_valuecodes',array($this, 'getValuecodes'))
        );
    }
}