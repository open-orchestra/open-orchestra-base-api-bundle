<?php

namespace OpenOrchestra\BaseApi\EventSubscriber;

use OpenOrchestra\BaseApi\Exceptions\HttpException\ApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class HttpExceptionSubscriber
 */
class HttpExceptionSubscriber implements EventSubscriberInterface
{
    protected $controller;

    /**
     * @param string $controller
     */
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!($exception = $event->getException()) instanceof ApiException ) {
            return;
        }

        $request = $event->getRequest();
        $attributes = array(
            '_controller' => $this->controller,
            'exception' => $exception,
            'format' => $request->getRequestFormat('json'),
        );
        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod('GET');

        $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, true);
        $event->setResponse($response);
        $event->stopPropagation();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 1000),
        );
    }
}
