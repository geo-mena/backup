<?php

use LaravelZero\Framework\Application;
use LaravelZero\Framework\Kernel;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
*/

$app = new Application(
    dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
*/

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Illuminate\Foundation\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Register Service Provider
|--------------------------------------------------------------------------
*/

$app->register(\Geomx\DatabaseBackup\Providers\BackupServiceProvider::class);

return $app;
