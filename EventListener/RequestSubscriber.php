<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 05/08/19
 * Time: 15:00
 */
namespace Lch\MaintenanceBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RequestSubscriber
 */
class RequestSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest'
        ];
    }


    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event) {

        if(!$event->isMasterRequest()) {
            return;
        }
    }
}