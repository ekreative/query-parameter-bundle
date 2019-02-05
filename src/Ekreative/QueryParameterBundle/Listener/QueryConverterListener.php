<?php

declare(strict_types=1);

namespace Ekreative\QueryParameterBundle\Listener;

use Ekreative\QueryParameterBundle\Manager\QueryManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if ($configurations = $request->attributes->get('_queries')) {
            $this->queryManager->manage($request, (array) $configurations);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
