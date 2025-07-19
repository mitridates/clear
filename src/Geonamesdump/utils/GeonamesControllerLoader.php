<?php
namespace App\Geonamesdump\utils;
use App\Geonamesdump\Loader\CountryLoader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Load Country and administrative divisions from controller
 */
class GeonamesControllerLoader
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @throws \Exception
     */
    public function loadCountry(string $country, string $cacheDir){

        $array = explode("\\",CountryLoader::class);
        array_pop($array);
        $loaderNamespace = implode("\\",$array).'\\';

        $config= Yaml::parseFile(__DIR__ . '/../Resources/config/parameters.yml');
        $gd= $config['parameters']['geonames_dump'];
        $gd['config']['tmpdir']= preg_replace('/%kernel.cache_dir%/i', $cacheDir, $gd['config']['tmpdir']);
        $loaderParameters = $gd;

        $loaderParameters['dump']=[
            'country' => [$country],
            'admin1' => [$country],
            'admin2' => [$country],
            'admin3' => [$country]
        ];
        $loaders = array_keys($loaderParameters['dump']);
        $repositoryHelper = new GeonamesRepositoryHelper($this->em);
        $fileHelper = new GeonamesFileHelper($loaderParameters['config']['webdir'], $loaderParameters['config']['localdir'], $loaderParameters['config']['tmpdir']);

        foreach($loaders as $key => $loader) {

            if($key==0) $fileHelper->createTempDir();
            $class = $loaderNamespace.ucfirst($loader).'Loader';

            $loader = new $class($fileHelper, $repositoryHelper, $loaderParameters, false);
            $loader->load();

            if($key === count($loaders)-1){

                if($loaderParameters['config']['rmdir']) $fileHelper->deleteTempDir();
                break;
            };
        }
        return $this;
    }
}