<?php
namespace App\Shared\Infrastructure\EventSubscriber;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string Locale in bundle parameters or Symfony global %kernel.default_locale%
     */
    private string $defaultLocale;

    private RequestStack $session;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param RequestStack $session
     */
        public function __construct(ParameterBagInterface $parameterBag, RequestStack $session)
    {
        $locales = $parameterBag->get('cave')['locales'];
        $locales= ($locales)? array_keys($locales) : ['en'];
        $this->defaultLocale = $locales[0] ?? $parameterBag->get('kernel.default_locale');
        $this->session = $session;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession())
        {
            $request->setSession($this->session->getSession());
        }

        // Check _locale routing parameter
        if ($locale = $request->attributes->get('_locale'))
        {
            $request->getSession()->set('_locale', $locale);

        }else{
            // Nothing in request, use one from parameters || kernel.default_locale
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 20)),
        );
    }
}
