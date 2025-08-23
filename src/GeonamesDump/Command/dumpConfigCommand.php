<?php
namespace App\GeonamesDump\Command;
use App\GeonamesDump\Model\LoaderInteface;
use App\GeonamesDump\utils\GeonamesFileHelper;
use App\GeonamesDump\utils\GeonamesRepositoryHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;


class dumpConfigCommand extends Command
{

    private OutputInterface $output;

    private InputInterface $input;

    private QuestionHelper $helper;

    private array $parameters;

    public function __construct(ParameterBagInterface $bag, private readonly ManagerRegistry $doctrine) {
        parent::__construct();
        $config= Yaml::parseFile(__DIR__ . '/../Resources/config/parameters.yml');
        $gd= $config['parameters']['geonames_dump'];

        $gd['config']['tmpdir']= preg_replace('/%kernel.cache_dir%/i', $bag->get('kernel.cache_dir'), $gd['config']['tmpdir']);
        $this->parameters = $gd; //$config['parameters']['geonames_dump'];

        //$this->parameters = $bag->get('geonames_dump');
    }

    protected function configure(): void
    {
        $this
            ->setName("geonamesdump:config")
            ->addArgument('loader',InputArgument::OPTIONAL, 'Loader name (optional)')
            ->addOption(
                'test',
                't',
                InputOption::VALUE_NONE,
                'Overwrite flush option to false.'
            )
            ->setDescription('load cvs files to local database')
            ->setHelp('Configure dump in parameters.yml and run available loaders.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->input = $input;
        $l = strtolower((string) $input->getArgument('loader'));
        $this->helper = $this->getHelper('question');

        if($input->getOption('test')){
            $this->parameters['config']['flush']= false;
        }

        if(!is_array($this->parameters['dump']))
        {
            $this->output->writeln("<error>No dump array found configuration!</error>");
            $this->output->writeln("<info>set \"geonames_dump[dump[]]\" array in parameters.yml (see: sample.xml)</info>");
            $this->output->writeln("<info>or use </info><comment>bin/console geonames:dump:country</comment> <info>to load countries with all administrative division</info>");
            return 0;
        }

        if($l == null){
            $l= $this->askForLoader();
        }else{
            if(in_array($l,array_keys($this->parameters['loaders']))){
                $l = (array)$l;
            }else{
                $this->output->writeln(sprintf('<error>Unknow loader: %s</error>',$l));
                $this->output->writeln(sprintf('<error>See config.yml: geonames_dump[loaders[...]] if %s is commented or a valid loader</error>',$l));
                $l = $this->askForLoader();
            }
        }

        if(!count($l)){
            $this->output->writeln(sprintf("No loader selected. Bye!"));
            return 0;
        }else{
            $question = new ConfirmationQuestion(sprintf('Confirm to run loaders: <info>%s</info> [y/n]', implode(',', $l)));
            if(!$this->helper->ask($this->input, $this->output, $question)){
                return 0;
            }
        }

        $this->load($l);
        return 1;
    }

    /**
     * Ask for loader.
     *
     * @return array
     */
    private function askForLoader()
    {
        $l= [];
        $this->output->writeln('Add loader from list. Press <info><Enter></info> to run selected loaders.');
        $loaders= array_keys($this->parameters['loaders']);

        do{
            $this->output->writeln(sprintf('Available loaders:<comment>%s</comment>',implode(', ', $loaders)));
            if(count($l)){
                $this->output->writeln(sprintf('Selected loaders: <info>%s</info>',implode(',', $l)));
            }

            $reply = strtolower((string) $this->helper->ask($this->input, $this->output, new Question('Add Loader: ')));

            if($reply===''){
                return $l;
            }elseif(in_array($reply, ['quit', 'exit'])){
                return [];
            }elseif($reply=='*'){
                 return array_keys($this->parameters['loaders']);
            }elseif(!preg_match("/^[A-Z0-9]+$/i",$reply) || empty($reply)){
                continue;
            }

            $selec = (in_array($reply,$loaders))? (array)$reply : preg_grep ( "/{$reply}/i" , $loaders);
//            if(in_array($reply,$loaders)){
//                $selec = (array)$reply;
//            }else{
//                $selec = preg_grep ( "/{$reply}/i" , $loaders);
//            }

            if(count($selec)>0){
                $l=  array_merge($l,$selec);
                $loaders= array_diff($loaders,$l);
            }else{
                $this->output->writeln(sprintf('<error>Unknow loader: %s</error>',$reply));
            }

        }while(count($loaders));

        return $l;
    }

    /**
     * Run loaders
     *
     * @param array $loaders loaders
     * @return bool
     *
     * @throws \Exception
     */
    private function load(array  $loaders)
    {
        $this->parameters['selected_loaders']= $loaders;
        $config = $this->parameters['config'];
        $ordered = [];
        $loaderNamespace = 'App\Geonamesdump\Loader\\';
        $repositoryHelper = new GeonamesRepositoryHelper($this->doctrine->getManager());
        $fileHelper = new GeonamesFileHelper($config['webdir'], $config['localdir'], $config['tmpdir']);

        foreach(array_keys($this->parameters['loaders']) as $l){
            if(in_array($l, $loaders)){
                $ordered [] = $l;
            }            
        }

        foreach($ordered as $key => $item) {

            if($key==0) $fileHelper->createTempDir();

            $class = $loaderNamespace.ucfirst($item).'Loader';
            /**
             * @var LoaderInteface $loader
             */
            $loader = new $class($fileHelper, $repositoryHelper, $this->parameters);
            if($key===0) $fileHelper->createTempDir();
            $loader->load();
            if($key == count($ordered)-1 && $this->parameters['config']['rmdir']) $fileHelper->deleteTempDir();
        }
        return true;

    }

}
