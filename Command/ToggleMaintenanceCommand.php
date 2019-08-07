<?php

namespace Lch\MaintenanceBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ToggleMaintenanceCommand
 *
 * @package Lch\MaintenanceBundle\Command
 */
class ToggleMaintenanceCommand extends Command
{


    public const MAINTENANCE_ON = 1;
    public const MAINTENANCE_OFF = 0;
    public const FILE_NAME = '.maintenance';
    public const MAINTENANCE_PARAMETER = 'maintenanceModeValue';

    /** @var string  */
    public static $defaultName = 'lch:maintenance:toggle';

    /** @var string */
    protected $projectDir;


    /**
     * ToggleMaintenanceCommand constructor.
     *
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Toggle maintenance mode')
            ->addArgument(
                static::MAINTENANCE_PARAMETER,
                InputArgument::REQUIRED,
                "The value to toggle maintenance mode to : 1 or 0",
                null
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $maintenanceModeValue = $input->getArgument('maintenanceModeValue');
        if (! in_array($maintenanceModeValue, [static::MAINTENANCE_OFF, static::MAINTENANCE_ON])) {
            throw new InvalidArgumentException('maintenanceModeValue have to be 1 or 0');
        }

        $filesystem = new Filesystem();

        switch($maintenanceModeValue) {
            case static::MAINTENANCE_ON:
                // Create .maintenance file
                $filesystem->touch($this->projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . static::FILE_NAME);
                break;
            case static::MAINTENANCE_OFF:
                $filesystem->remove($this->projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . static::FILE_NAME);
                break;
        }
    }
}
