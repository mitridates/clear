<?php
namespace App\Twig;
use App\Domain\Fielddefinition\Manager\FieldDefinitionManager;
use App\Services\Cache\FilesCache\FieldDefinitionCache;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Fielddefinition twig extension
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
     * Get Fielddefinition in JSON:API spec format by code and locale
     *
     * @param int $code
     * @param ?string $locale
     * @return ?array
     * @throws InvalidArgumentException
     */
    public function getFieldDefinitionJsonByCode(int $code, ?string $locale = null): ?array
    {
        // Intenta obtener la definición desde la caché
        $fdJson = $this->cache->getFieldDefinition($code);

        if ($fdJson) {
            return $fdJson;
        }

        // Si no está en la caché, intenta buscarlo en la base de datos
        $fd = $this->fdManager->repo->find($code);
        if (!$fd) {
            return null;
        }

        // Genera y guarda la definición en la caché
        $this->cache->warmup($this->fdManager, $fd, $this->urlGenerator);

        // Recupera la definición ya con el posible locale si aplica
        return $this->cache->getFieldDefinition($code, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return array(
            new TwigFunction('get_field_definition_json_by_id',array($this, 'getFieldDefinitionJsonByCode')),
        );
    }
}