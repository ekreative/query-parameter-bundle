<?php

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

    /**
     * @return QueryManager
     */
    public function getQueryManager()
    {
        return $this->queryManager;
    }

    /**
     * @param QueryManager $queryManager
     */
    public function setQueryManager($queryManager)
    {
        $this->queryManager = $queryManager;
    }

    /**
     * Modifies the QueryManager instance.
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if ($configurations = $request->attributes->get('_queries')) {
            $this->getQueryManager()->manage(
                $request,
                is_array($configurations) ? $configurations : [$configurations]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
