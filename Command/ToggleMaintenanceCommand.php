<?php

namespace Lch\MaintenanceBundle\Command;

use Doctrine\DBAL\Exception\ReadOnlyException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
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
    public const MAINTENANCE_PARAMETER = 'mode';
    public const FILE_PARAMETER = 'file';
    public const MAINTENANCE_TAG = '### Maintenance ###';

    /** @var string */
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
            ->addOption(
                static::MAINTENANCE_PARAMETER,
                'm',
                InputOption::VALUE_REQUIRED,
                "The value to toggle maintenance mode to : 1 or 0",
                null
            )
            ->addOption(
                static::FILE_PARAMETER,
                'f',
                InputOption::VALUE_OPTIONAL,
                "The HTML file absolute path for splash screen",
                ''
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
        $maintenanceModeValue = $input->getOption(static::MAINTENANCE_PARAMETER);
        if (! in_array($maintenanceModeValue, [static::MAINTENANCE_OFF, static::MAINTENANCE_ON])) {
            throw new InvalidArgumentException('maintenanceModeValue have to be 1 or 0');
        }

        $maintenanceHtmlFilePath = $input->getOption(static::FILE_PARAMETER);
        if(empty($maintenanceHtmlFilePath)) {
            $maintenanceHtmlFilePath = dirname(__FILE__) . '/../Resources/public/maintenance.html';
        }
        else if(!file_exists($maintenanceHtmlFilePath)) {
            throw new FileNotFoundException('Maintenance HTML file not found');
        }

        $htaccessFilePath = $this->projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . '.htaccess';

        if (! file_exists($htaccessFilePath)) {
            throw new FileNotFoundException('.htaccess file not found. Should be present');
        }

        $fp = fopen($htaccessFilePath, 'a');
        if (! $fp) {
            throw new ReadOnlyException('.htaccess file is not writable');
        }


        $fileContent        = file_get_contents($htaccessFilePath);
        $maintenancePattern = '/(?<=' . static::MAINTENANCE_TAG . ')(.*)(?=' . static::MAINTENANCE_TAG . ')/s';
        $maintenanceRules = '';

        switch ($maintenanceModeValue) {
            case static::MAINTENANCE_ON:
                $maintenanceRules   = "\r\nRewriteCond %{REQUEST_URI} !/" . pathinfo($maintenanceHtmlFilePath, PATHINFO_BASENAME) . "$ [NC]\r\nRewriteCond %{REQUEST_URI} !\.(jpe?g?|png|gif) [NC]\r\nRewriteRule $ /maintenance.html [R=302,L]\r\n";
                copy($maintenanceHtmlFilePath, $this->projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . pathinfo($maintenanceHtmlFilePath, PATHINFO_BASENAME));
                $output->writeln("Maintenance file copied to public/");

                $output->writeln("Maintenance rules added on .htaccess");

                break;
            case static::MAINTENANCE_OFF:
                $maintenanceRules   = "\r\nRewriteCond %{REQUEST_URI} " . pathinfo($maintenanceHtmlFilePath, PATHINFO_BASENAME) . "$ [NC]\r\nRewriteCond %{REQUEST_URI} !\.(jpe?g?|png|gif) [NC]\r\nRewriteRule $ / [R=301,L]\r\n";
                $output->writeln("Maintenance rules removed from .htaccess");
                break;
        }

        file_put_contents(
            $htaccessFilePath,
            preg_replace($maintenancePattern, $maintenanceRules, $fileContent)
        );

        fclose($fp);
    }
}
