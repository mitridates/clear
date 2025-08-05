<?php
namespace App\Shared\Infrastructure\EventSubscriber;
use App\Services\Cache\FilesCache\DbStatusCache;
use App\Shared\Manager\SetupManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

;

/**
 * Check for one country or organisation and Check site parameters.
 * @package App\EventListener
  */
class ParamSetupSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly EntityManagerInterface $em,
                                private readonly UrlGeneratorInterface $urlGenerator,
                                private readonly DbStatusCache $cache
    )
    {
    }


    /**
     * @throws InvalidArgumentException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        if ( HttpKernelInterface::MAIN_REQUEST != $event->getRequestType() ) {
            return;// don't do anything if it's not the master request
        }
        $route= $event->getRequest()->get('_route');

        if(!str_starts_with($route, 'admin_')){
            return;
        }

        //get cache or update
        $data= $this->cache->getDataBaseStatus();
        if(!$data){
            $data = $this->cache->updateDataBaseStatus((new SetupManager($this->em))->getDataBaseStatus());
        }


        if($data['countryCount']===0)
        {
            if( $route==='admin_install_geonames') return;

            $event->setController(function (){
                return new RedirectResponse($this->urlGenerator->generate('admin_install_geonames'));
            });
            return;
        }

        if(!$data['fdCount'] || !$data['fvcCount'])
        {
            if( $route==='admin_install_field_definition') return;
            $event->setController(function (){
                return new RedirectResponse($this->urlGenerator->generate('admin_install_field_definition'));
            });
            return;
        }

        if(!$data['orgCount'])
        {
            if( $route==='admin_install_organisation') return;
            $event->setController(function (){
                return new RedirectResponse($this->urlGenerator->generate('admin_install_organisation'));
            });
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::CONTROLLER => array(array('onKernelController', 15)),
        );
    }
}
