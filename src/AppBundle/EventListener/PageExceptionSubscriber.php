<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * User: lewisbriffa
 * Date: 26/12/2017
 * Time: 17:51
 */

class PageExceptionSubscriber implements EventSubscriberInterface
{

    protected $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();

        if($e instanceof NotFoundHttpException) {

            $this->createFlashMessage('error', 'The requested page was not found');

            $url = $this->router->generate('worth_reading');

            $event->setResponse(new RedirectResponse($url));

        } else if($e instanceof AccessDeniedHttpException) {

            $this->createFlashMessage('error', 'Access Denied');

            $url = $this->router->generate('worth_reading');

            $event->setResponse(new RedirectResponse($url));

        }
    }

    private function createFlashMessage(string $type, string $message)
    {
        $session = new Session();
        $session->getFlashBag()->add($type, $message);
    }
}