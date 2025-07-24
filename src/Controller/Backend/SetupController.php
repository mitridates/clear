<?php
namespace App\Controller\Backend;
use App\Domain\Geonames\Import\Sql\ImportSql;
use App\Form\backend\setup\SetupCountryType;
use App\Form\backend\setup\SetupSqlLoaderType;
use App\Geonamesdump\utils\GeonamesControllerLoader;
use App\Manager\SetupManager;
use App\Services\Cache\FilesCache\DbStatusCache;
use App\Shared\Arraypath;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/setup')]
class SetupController extends AbstractController
{
    #[Route(path: '/', name: 'admin_setup_index')]
    public function indexAction(DbStatusCache $cache): Response
    {
        return $this->render('@admin/setup/index.html.twig',['data'=>$cache->getDataBaseStatus()]);
    }


    /**
     * @throws InvalidArgumentException
     */
    #[Route(path: '/geonames', name: 'admin_setup_geonames')]
    public function setupGeonamesAction(Request $request,EntityManagerInterface $em, ParameterBagInterface $bag): Response
    {
        $projectDir= $this->getParameter('kernel.project_dir');
        $cacheDir= $this->getParameter('kernel.cache_dir');
        $env= $this->getParameter('kernel.environment');

        $cache= new DbStatusCache($projectDir, $env);
        $form = $this->createForm(SetupCountryType::class, null)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('country')->getData())
        {
            $controllerLoader = new GeonamesControllerLoader($em);

            $controllerLoader->loadCountry($form->get('country')->getData(), $cacheDir);

            $cache->updateDataBaseStatus((new SetupManager($em))->getDataBaseStatus());

            return $this->redirectToRoute('admin_setup_geonames');
        }
        return $this->render('@admin/setup/geonames.html.twig',['form'=>$form, 'data'=>$cache->getDataBaseStatus()]);
    }


    /**
     * @throws \Exception|InvalidArgumentException
     */
    #[Route(path: '/fielddefinition', name: 'admin_setup_field_definition')]
    public function setupFieldDefinitionAction(Request $request, ParameterBagInterface $bag, DbStatusCache $cache, EntityManagerInterface $em): Response
    {
        $params= new Arraypath($bag->get('cave'));
        $files = $params->get('install', []);
        $form = $this->createForm(SetupSqlLoaderType::class, null, [
            'install'=>$files
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('files')->getData())
        {
            $dir= $params->get('install:dir');
            $file= $form->get('files')->getData();
            ImportSql::loadSql($em->getConnection()->getNativeConnection(), $dir.$file);
            $cache->updateDataBaseStatus((new SetupManager($em))->getDataBaseStatus());
            $this->redirectToRoute('admin_setup_index');
        }

        return $this->render('@admin/setup/field_definition.html.twig',['data'=>$cache->getDataBaseStatus(), 'form'=> $form->createView()]);
    }

}