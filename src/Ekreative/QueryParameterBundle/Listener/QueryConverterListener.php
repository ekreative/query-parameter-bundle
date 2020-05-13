<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Listener;

use Ekreative\QueryParameterBundle\Manager\QueryManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class QueryConverterListener implements EventSubscriberInterface
{
    /**
     * @var QueryManager
     */
    private $queryManager;

    public function __construct(QueryManager $queryManager)
    {
        $this->queryManager = $queryManager;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $request = $event->getRequest();
        $this->handleRequest($request);
    }

    public function onKernelControllerCompat(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $this->handleRequest($request);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => class_exists(FilterControllerEvent::class) ? 'onKernelControllerCompat' : 'onKernelController',
        ];
    }

    private function handleRequest(Request $request): void
    {
        if ($configurations = $request->attributes->get('_queries')) {
            $this->queryManager->manage($request, (array) $configurations);
        }
    }
}
