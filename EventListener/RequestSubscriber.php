<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 05/08/19
 * Time: 15:00
 */

namespace Lch\MaintenanceBundle\EventListener;

use Lch\MaintenanceBundle\Command\ToggleMaintenanceCommand;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RequestSubscriber
 */
class RequestSubscriber implements EventSubscriberInterface
{

    /** @var string */
    protected $projectDir;


    /** @var FileLocatorInterface */
    protected $fileLocator;

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
     * RequestSubscriber constructor.
     *
     * @param string $projectDir
     */
    public function __construct(string $projectDir, FileLocatorInterface $fileLocator)
    {
        $this->projectDir  = $projectDir;
        $this->fileLocator = $fileLocator;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {

        if (! $event->isMasterRequest()) {
            return;
        }

        $filesystem = new FileSystem();
        // Check if file exist
        if ($filesystem->exists($this->projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . ToggleMaintenanceCommand::FILE_NAME)) {
            $event->setResponse(new Response(
                // TODO make this configurable
                file_get_contents($this->fileLocator->locate('@LchMaintenanceBundle/Resources/public/maintenance.html')),
                Response::HTTP_SERVICE_UNAVAILABLE
            ));
        }
    }
}