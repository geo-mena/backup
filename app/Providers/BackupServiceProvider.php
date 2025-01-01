<?php

namespace Geomx\DatabaseBackup\Providers;

use Illuminate\Support\ServiceProvider;
use Geomx\DatabaseBackup\Commands\BackupCommand;
use Geomx\DatabaseBackup\Commands\BackupTUICommand;

class BackupServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupCommand::class,
                BackupTUICommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/backup.php',
            'backup'
        );
    }
}
