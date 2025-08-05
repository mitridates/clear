<?php
namespace App\Install\Infrastucture\Controller;
use App\Domain\Geonames\Geonamesdump\utils\GeonamesControllerLoader;
use App\Geonames\Domain\Import\Sql\ImportSql;
use App\Install\Infrastucture\{Form\SetupCountryType};
use App\Install\Infrastucture\Form\InstallOrganisationType;
use App\Organisation\Domain\Entity\Organisation;
use App\Services\Cache\FilesCache\DbStatusCache;
use App\Shared\Arraypath;
use App\Shared\Manager\SetupManager;
use App\SystemParameter\Domain\Entity\{SystemParameter};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/install')]
class InstallController extends AbstractController
{
    #[Route(path: '/geonames', name: 'admin_install_geonames')]
    public function installGeonamesAction(Request $request,EntityManagerInterface $em, ParameterBagInterface $bag, DbStatusCache $cache): Response
    {
        $status= $cache->getDataBaseStatus();
;
        if($status['countryCount']>0){
            return $this->redirectToRoute('admin_setup_geonames');
        }

        if($cache->getDataBaseStatus())
        $form = $this->createForm(SetupCountryType::class, null)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $form->get('country')->getData()) {
            $controllerLoader = new GeonamesControllerLoader($em);
            $controllerLoader->loadCountry($form->get('country')->getData(), $bag->get('kernel.cache_dir'));
            $cache->updateDataBaseStatus((new SetupManager($em))->getDataBaseStatus());
            return $this->redirectToRoute('admin_setup_geonames');
        }
        return $this->render('@admin/install/install_geonames.twig',['form'=>$form, 'data'=>$cache->getDataBaseStatus()]);
    }


    /**
     * @throws \Exception
     */
    #[Route(path: '/fieldDefinition', name: 'admin_install_field_definition')]
    public function installFieldDefinitionAction(Request $request, EntityManagerInterface $em, ParameterBagInterface $bag): Response
    {
        $cache= new DbStatusCache($bag->get('kernel.project_dir'), $bag->get('kernel.environment'));
        $token= $request->request->get('token');
        $caveParameters= new Arraypath($bag->get('cave'));

        if ($token && $this->isCsrfTokenValid('install_token', $token))
        {

            $db= $em->getConnection()->getNativeConnection();
            $dir= $caveParameters->get('install:dir', $bag->get('kernel.project_dir').'/Resources/install/');

            $filename = $caveParameters->get('install:files:field_definition:file');
            ImportSql::loadSql($db, $dir.$filename);
            $filename = $caveParameters->get('install:files:field_value_code:file');
            ImportSql::loadSql($db, $dir.$filename);
            $cache->updateDataBaseStatus((new SetupManager($em))->getDataBaseStatus());
            return $this->redirectToRoute('admin_install_field_definition');
        }

        return $this->render('@admin/install/install_field_definition.twig',['data'=>$cache->getDataBaseStatus()]);

    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/organisation', name: 'admin_install_organisation')]
    public function installOrganisationAction(Request $request,EntityManagerInterface $em, DbStatusCache $cache): Response
    {
        $form = $this->createForm(InstallOrganisationType::class, new Organisation())->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var Organisation $organisation
             */
            $organisation = $form->getData();
            $metadata = $em->getClassMetaData(get_class($organisation));
            $metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            $em->persist($organisation);
            $em->flush();

            //set initial system parameters
            $sysParam = new SystemParameter();
            $sysParam->setOrganisationdbm($organisation)
                ->setName('Default parameters')
                ->setActive(true)
                ->setCountry($organisation->getCountry())
                ->setLanguage(strtolower($organisation->getCountry()->getId()));
            $em->persist($sysParam);
            $em->flush();
            $cache->updateDataBaseStatus((new SetupManager($em))->getDataBaseStatus());
            return $this->redirectToRoute('admin_setup_index');
        }

        return $this->render('@admin/setup/install_organisation.twig',['data'=> $cache->getDataBaseStatus(), 'form'=>$form]);
    }

}