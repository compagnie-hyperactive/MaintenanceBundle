<?php


namespace Lch\MaintenanceBundle\Tests\Command;


use Lch\MaintenanceBundle\Command\ToggleMaintenanceCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeTest extends WebTestCase
{

    /** @var Command */
    protected $maintenanceCommand;

    /** @var CommandTester */
    protected $commandTester;

    /** @var string */
    protected $exposedDir;

    /** @var Filesystem */
    protected $filesystem;

    /** @var Client */
    protected $client;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        static::bootKernel();
        $this->exposedDir         = static::$kernel
                                        ->getContainer()
                                        ->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public';
        $application              = new Application(static::$kernel);
        $this->maintenanceCommand = $application->find(ToggleMaintenanceCommand::$defaultName);
        $this->commandTester      = new CommandTester($this->maintenanceCommand);
        $this->filesystem         = new Filesystem();
        $this->client             = static::createClient();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        $this->exposedDir         = null;
        $this->maintenanceCommand = null;
        $this->commandTester      = null;
        $this->filesystem         = null;
    }

    /**
     * Check maintenance flag proper addition
     */
    public function testMaintenanceSet()
    {
        $this->commandTester->execute(
            [
                'command'                                       => $this->maintenanceCommand->getName(),
                ToggleMaintenanceCommand::MAINTENANCE_PARAMETER => 1,
            ]
        );

        $this->assertTrue(
            $this
                ->filesystem
                ->exists($this->exposedDir . DIRECTORY_SEPARATOR . ToggleMaintenanceCommand::FILE_NAME)
        );

        $this->client->request('GET', '/');
        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Check maintenance flag proper deletion
     */
    public function testMaintenanceUnset()
    {
        $this->commandTester->execute(
            [
                'command'                                       => $this->maintenanceCommand->getName(),
                ToggleMaintenanceCommand::MAINTENANCE_PARAMETER => 0,
            ]
        );

        $this->assertFalse(
            $this
                ->filesystem
                ->exists($this->exposedDir . DIRECTORY_SEPARATOR . ToggleMaintenanceCommand::FILE_NAME)
        );

        $this->client->request('GET', '/');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}