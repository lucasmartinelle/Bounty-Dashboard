<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    // Default language
    private $defaultLocale;

    /**
     * update locale when needed
     */
    public function onKernelRequest(RequestEvent $event)
    {
        // get request
        $request = $event->getRequest();

        if(!$request->hasPreviousSession() or !$request->getSession()->get('_locale_user')){
            $this->defaultLocale = $request->getPreferredLanguage(['en', 'fr']);
        } else {
            $this->defaultLocale = $request->getSession()->get('_locale', 'en');
        }

        // We searching for locale in the URL
        if ($locale = $request->query->get('_locale')) {
            // if it is the case, update locale
            $request->setLocale($locale);
        } else {
            // Else, use default one
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // Define higher priority
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}