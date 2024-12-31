<?php

namespace App\Commands;

use Exception;
use LaravelZero\Framework\Commands\Command;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;

class BackupTUICommand extends Command
{
    protected $signature = 'backup:tui';
    protected $description = 'Interactive TUI for database backups';

    private $config = [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => null,
        'database' => '',
        'user' => '',
        'password' => '',
        'output' => null,
        'compress' => false
    ];

    private BackupCommand $backupCommand;

    public function __construct()
    {
        parent::__construct();
        $this->backupCommand = new BackupCommand();
    }

    public function handle()
    {
        $menu = (new CliMenuBuilder)
            ->setTitle('DATABASE BACKUP UTILITY')
            ->setWidth(80)
            ->setPadding(2, 4)
            ->setMarginAuto()
            ->setBackgroundColour('23')
            ->setForegroundColour('40')
            ->setTitleSeparator('-')
            ->addLineBreak()
            ->addAsciiArt("
    ____             __                 __  ____
   / __ )____ ______/ /____  ______  _ / / / / /
  / __  / __ `/ ___/ //_/ / / / __ \(_) /_/ / 
 / /_/ / /_/ / /__/ ,< / /_/ / /_/ / / __  /  
/_____/\__,_/\___/_/|_|\__,_/ .___/_/_/ /_/   
                           /_/                  
            ")
            ->addLineBreak()
            ->addLineBreak('-')
            ->addStaticItem('Select your database type:')
            ->addLineBreak('-')
            ->addLineBreak()
            ->addItem('🐬 MySQL Database', function (CliMenu $menu) {
                $this->config['type'] = 'mysql';
                $this->config['port'] = 3306;
                $this->showConfigMenu($menu);
            })
            ->addItem('🐘 PostgreSQL Database', function (CliMenu $menu) {
                $this->config['type'] = 'postgres';
                $this->config['port'] = 5432;
                $this->showConfigMenu($menu);
            })
            ->addItem('🍃 MongoDB Database', function (CliMenu $menu) {
                $this->config['type'] = 'mongodb';
                $this->config['port'] = 27017;
                $this->showConfigMenu($menu);
            })
            ->addLineBreak()
            ->setExitButtonText('❌ EXIT')
            ->build();

        return $menu->open();
    }

    private function showConfigMenu(CliMenu $parentMenu)
    {
        $statusItems = $this->getConfigStatus();

        $configMenu = (new CliMenuBuilder)
            ->setTitle("⚙️ Configure {$this->config['type']} Backup")
            ->setWidth(80)
            ->setPadding(2, 4)
            ->setMarginAuto()
            ->addStaticItem('Current Configuration:')
            ->addLineBreak()
            ->addStaticItem($statusItems)
            ->addLineBreak()
            ->addStaticItem('Select an option to modify:')
            ->addLineBreak()
            ->addItem('🚀 ' . 'Connection Settings', function (CliMenu $menu) {
                $this->configureConnection($menu);
            })
            ->addItem('📦 ' . 'Database Settings', function (CliMenu $menu) {
                $this->configureDatabase($menu);
            })
            ->addItem('📂 ' . 'Output Settings', function (CliMenu $menu) {
                $this->configureOutput($menu);
            })
            ->addLineBreak()
            ->addItem('▶️ Start Backup', function (CliMenu $menu) use ($parentMenu) {
                if ($this->validateConfig()) {
                    $menu->close();
                    $parentMenu->close();
                    $this->executeBackup();
                }
            })
            ->addItem('⬅️ Back to Main Menu', function (CliMenu $menu) {
                $menu->close();
            })
            ->build();

        return $configMenu->open();
    }

    private function configureConnection(CliMenu $menu)
    {
        $menu->close();

        $this->newLine();
        $this->info('📝 Connection Configuration');
        $this->newLine();

        $this->config['host'] = $this->ask('Enter host (default: localhost):') ?: 'localhost';
        $this->config['port'] = (int)$this->ask("Enter port (default: {$this->config['port']}):");
        $this->config['user'] = $this->ask('Enter username:');
        $this->config['password'] = $this->secret('Enter password:');

        $this->newLine();
        $this->info('✅ Connection settings updated');
        $this->newLine();
        sleep(1); 

        $this->refreshConfigMenu($menu);
    }

    private function configureDatabase(CliMenu $menu)
    {
        $menu->close();

        $this->newLine();
        $this->info('📝 Database Configuration');
        $this->newLine();

        $this->config['database'] = $this->ask('Enter database name:');

        $this->newLine();
        $this->info('✅ Database settings updated');
        $this->newLine();
        sleep(1);

        $this->refreshConfigMenu($menu);
    }

    private function configureOutput(CliMenu $menu)
    {
        $menu->close();

        $this->newLine();
        $this->info('📝 Output Configuration');
        $this->newLine();

        $this->config['output'] = $this->ask('Enter output directory (leave empty for default):');
        $this->config['compress'] = $this->confirm('Compress backup file?', true);

        $this->newLine();
        $this->info('✅ Output settings updated');
        $this->newLine();
        sleep(1);

        $this->refreshConfigMenu($menu);
    }

    private function getConfigStatus(): string
    {
        $status = [];
        $status[] = "Host: " . ($this->config['host'] ?: '<not set>');
        $status[] = "Port: " . ($this->config['port'] ?: '<not set>');
        $status[] = "Database: " . ($this->config['database'] ?: '<not set>');
        $status[] = "Username: " . ($this->config['user'] ?: '<not set>');
        $status[] = "Password: " . ($this->config['password'] ? '****' : '<not set>');
        $status[] = "Output: " . ($this->config['output'] ?: '<default>');
        $status[] = "Compress: " . ($this->config['compress'] ? 'Yes' : 'No');

        return implode("\n", array_map(fn($item) => "  {$item}", $status));
    }

    private function executeBackup()
    {
        $this->info("\n🚀 Initiating backup process...");
        $this->newLine();
        $this->components->task("Validating configuration", fn() => true);
        $this->components->task("Preparing backup", fn() => true);

        try {
            $exitCode = $this->call('backup', [
                '--type' => $this->config['type'],
                '--host' => $this->config['host'],
                '--port' => $this->config['port'],
                '--database' => $this->config['database'],
                '--user' => $this->config['user'],
                '--password' => $this->config['password'],
                '--output' => $this->config['output'],
                '--compress' => $this->config['compress'],
            ]);

            if ($exitCode !== 0) {
                throw new Exception('Backup process failed');
            }

            $this->newLine();
            $this->info('✅ Backup completed successfully!');
            $this->newLine();
        } catch (Exception $e) {
            $this->newLine();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->newLine();
            return 1;
        }

        return 0;
    }

    private function validateConfig(): bool
    {
        if (empty($this->config['database'])) {
            $this->error('Database name is required');
            return false;
        }
        if (empty($this->config['host'])) {
            $this->error('Host is required');
            return false;
        }
        if (empty($this->config['user'])) {
            $this->error('Username is required');
            return false;
        }
        return true;
    }

    private function refreshConfigMenu(CliMenu $menu)
    {
        $menu->close();
        $this->showConfigMenu($menu);
    }
}
