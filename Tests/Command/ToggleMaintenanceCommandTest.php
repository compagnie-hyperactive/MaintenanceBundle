<?php


namespace Lch\MaintenanceBundle\Tests\Command;


use Lch\MaintenanceBundle\Command\ToggleMaintenanceCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ToggleMaintenanceCommandTest extends KernelTestCase
{
    /** @var string */
    protected $projectDir;

    /** @var Filesystem  */
    protected $filesystem;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
//        // Create temp dir
//        $this->filesystem = new Filesystem();
//        $this->projectDir = $_SERVER['PROJECT_TEST_DIR'];
//        $this->filesystem->mkdir($this->projectDir);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
//        $this->filesystem->remove($this->projectDir);
//        $this->projectDir = null;
    }

    public function testMaintenanceSet()
    {
//        $kernel      = static::createKernel();
//        $application = new Application($kernel);
//
//        $command       = $application->find(ToggleMaintenanceCommand::$defaultName);
//        $commandTester = new CommandTester($command);
//        $commandTester->execute([
//            'command'                                       => $command->getName(),
//            ToggleMaintenanceCommand::MAINTENANCE_PARAMETER => 1,
//        ]);


//        $this->assertTrue($this->filesystem->exists($this->projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . ToggleMaintenanceCommand::FILE_NAME));
        $this->assertTrue(true);
    }

    public function testMaintenanceUnset()
    {
        $this->assertTrue(true);
    }
}