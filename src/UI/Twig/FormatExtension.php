<?php
namespace App\UI\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

;

/**
 * Format vars
 *
 * @author mitridates
 */
class FormatExtension extends AbstractExtension
{
    /**
     * @param string $bytes
     * @return string
     */
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' FormatExtension.php' . $units[$pow];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() : array
    {
        return array(
            new TwigFunction('formatBytes',array($this, 'formatBytes')),
        );
    }
}