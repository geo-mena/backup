<?php

namespace App\Commands;

use Exception;
use LaravelZero\Framework\Commands\Command;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\MongoDB;
use Illuminate\Support\Facades\File;

use RuntimeException;
use Symfony\Component\Process\Process;

class BackupCommand extends Command
{
    protected $signature = 'backup
        {--database= : Nombre de la base de datos}
        {--type=mysql : Tipo de base de datos (mysql/postgres/mongodb)}
        {--host=localhost : Host de la base de datos}
        {--port= : Puerto de la base de datos}
        {--user= : Usuario de la base de datos}
        {--password= : Contraseña de la base de datos}
        {--output= : Directorio de salida}
        {--compress : Comprimir el backup}';

    protected $description = 'Crear backup de base de datos';

    /**
     * Ejecutar el comando de backup de base de datos
     *
     * @return mixed
     * @throws Exception
     * @throws RuntimeException
     */
    public function handle()
    {
        $type = $this->option('type');
        $database = $this->option('database');
        $output = $this->option('output') ?? storage_path('backups');

        // Validar datos requeridos
        if (empty($database)) {
            $this->error('El nombre de la base de datos es requerido');
            return 1;
        }

        // Crear directorio si no existe
        if (!File::exists($output)) {
            File::makeDirectory($output, 0755, true);
        }

        $this->info("Iniciando backup de {$database}...🚀");

        try {
            $outputPath = $this->getOutputPath();

            switch ($type) {
                case 'mysql':
                    $this->backupMysql($outputPath);
                    break;
                case 'postgres':
                    $this->backupPostgres($outputPath);
                    break;
                case 'mongodb':
                    $this->backupMongodb($outputPath);
                    break;
                default:
                    $this->error("Tipo de base de datos no soportado: {$type}");
                    return 1;
            }

            if ($this->option('compress')) {
                $this->compressBackup($outputPath);
            }

            $this->info('Backup completado exitosamente!🎉');
            return 0;
        } catch (Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Realiza un backup de una base de datos MySQL
     *
     * @param string $outputPath
     * @return void
     * @throws Exception
     */
    private function backupMysql($outputPath)
    {
        $port = $this->option('port') ?? 3306;

        $this->info("Preparando backup MySQL:");
        $this->info("- Host: " . $this->option('host'));
        $this->info("- Puerto: " . $port);
        $this->info("- Base de datos: " . $this->option('database'));
        $this->info("- Archivo de salida: " . $outputPath);

        try {
            $directory = dirname($outputPath);
            if (!File::exists($directory)) {
                $this->info("Creando directorio: " . $directory);
                File::makeDirectory($directory, 0755, true);
            }

            MySql::create()
                ->setHost($this->option('host'))
                ->setPort($port)
                ->setDbName($this->option('database'))
                ->setUserName($this->option('user'))
                ->setPassword($this->option('password'))
                ->addExtraOption('--routines')
                ->addExtraOption('--events')
                ->addExtraOption('--triggers')
                ->dumpToFile($outputPath);

            if (!File::exists($outputPath)) {
                throw new Exception("El archivo de backup no se creó correctamente");
            }

            $this->info("Archivo de backup creado exitosamente!📦️");
            $this->info("Tamaño del archivo: " . File::size($outputPath) . " bytes");
        } catch (Exception $e) {
            $this->error("Error detallado: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * !Realiza un backup de una base de datos PostgreSQL
     *
     * @param string $outputPath
     * @return void
     * @throws Exception
     */
    private function backupPostgres($outputPath)
    {
        $port = $this->option('port') ?? 5432;

        $this->info("Preparando backup PostgreSQL:");
        $this->info("- Host: " . $this->option('host'));
        $this->info("- Puerto: " . $port);
        $this->info("- Base de datos: " . $this->option('database'));
        $this->info("- Archivo de salida: " . $outputPath);

        try {
            $directory = dirname($outputPath);
            if (!File::exists($directory)) {
                $this->info("Creando directorio: " . $directory);
                File::makeDirectory($directory, 0755, true);
            }

            PostgreSql::create()
                ->setHost($this->option('host'))
                ->setPort($port)
                ->setDbName($this->option('database'))
                ->setUserName($this->option('user'))
                ->setPassword($this->option('password'))
                ->addExtraOption('--clean')
                ->addExtraOption('--if-exists')
                ->dumpToFile($outputPath);

            if (!File::exists($outputPath)) {
                throw new Exception("El archivo de backup no se creó correctamente");
            }

            $this->info("Archivo de backup creado exitosamente!📦️");
            $this->info("Tamaño del archivo: " . File::size($outputPath) . " bytes");
        } catch (Exception $e) {
            $this->error("Error detallado: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Realiza un backup de una base de datos MongoDB
     *
     * @param string $outputPath
     * @return void
     * @throws Exception
     */
    private function backupMongodb($outputPath)
    {
        $port = $this->option('port') ?? 27017;

        $this->info("Preparando backup MongoDB:");
        $this->info("- Host: " . $this->option('host'));
        $this->info("- Puerto: " . $port);
        $this->info("- Base de datos: " . $this->option('database'));
        $this->info("- Archivo de salida: " . $outputPath);

        try {
            $directory = dirname($outputPath);
            if (!File::exists($directory)) {
                $this->info("Creando directorio: " . $directory);
                File::makeDirectory($directory, 0755, true);
            }

            $auth = '';
            if ($this->option('user') && $this->option('password')) {
                $auth = sprintf(
                    '--username %s --password %s --authenticationDatabase admin',
                    escapeshellarg($this->option('user')),
                    escapeshellarg($this->option('password'))
                );
            }

            $command = sprintf(
                'mongodump --host %s --port %s %s --db %s --out %s',
                escapeshellarg($this->option('host')),
                $port,
                $auth,
                escapeshellarg($this->option('database')),
                escapeshellarg($this->getOutputPath())
            );

            $process = Process::fromShellCommandline($command);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new RuntimeException($process->getErrorOutput());
            }

            if (!File::exists($outputPath)) {
                throw new Exception("El archivo de backup no se creó correctamente");
            }

            $this->info("Archivo de backup creado exitosamente!📦️");
            $this->info("Tamaño del archivo: " . File::size($outputPath) . " bytes");
        } catch (Exception $e) {
            $this->error("Error detallado: " . $e->getMessage());
            throw $e;
        }
    }

    private function getOutputPath()
    {
        $output = $this->option('output') ?? storage_path('backups');
        $filename = sprintf(
            '%s_%s_%s.%s',
            $this->option('database'),
            $this->option('type'),
            date('Y-m-d_H-i-s'),
            $this->getFileExtension()
        );

        return $output . DIRECTORY_SEPARATOR . $filename;
    }

    private function getFileExtension()
    {
        switch ($this->option('type')) {
            case 'mysql':
                return 'sql';
            case 'postgres':
                return 'sql';
            case 'mongodb':
                return 'archive';
            default:
                return 'dump';
        }
    }

    private function compressBackup($outputPath)
    {
        $this->info("Comprimiendo backup...");

        if (!File::exists($outputPath)) {
            throw new Exception("No se encuentra el archivo a comprimir: " . $outputPath);
        }

        $zipPath = $outputPath . '.gz';
        $fp = gzopen($zipPath, 'w9');
        gzwrite($fp, file_get_contents($outputPath));
        gzclose($fp);

        if (file_exists($zipPath)) {
            unlink($outputPath);
            $this->info('Backup comprimido exitosamente!🔒️');
        } else {
            throw new Exception("No se pudo crear el archivo comprimido");
        }
    }
}
