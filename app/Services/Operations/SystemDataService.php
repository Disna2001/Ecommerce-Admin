<?php

namespace App\Services\Operations;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class SystemDataService
{
    protected array $excludeTables = [
        'migrations',
        'failed_jobs',
        'jobs',
        'job_batches',
        'cache',
        'cache_locks',
        'sessions',
    ];

    public function createBackup(): string
    {
        $backupName = 'backup-' . now()->format('Y-m-d-H-i-s') . '.zip';
        $backupPath = storage_path('app/backups/' . $backupName);
        
        if (!File::exists(storage_path('app/backups'))) {
            File::makeDirectory(storage_path('app/backups'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $tables = Schema::getTables();
            
            foreach ($tables as $table) {
                $tableName = is_array($table) ? ($table['name'] ?? null) : $table;
                if (!$tableName || in_array($tableName, $this->excludeTables)) {
                    continue;
                }

                $data = DB::table($tableName)->get()->toArray();
                $zip->addFromString($tableName . '.json', json_encode($data, JSON_PRETTY_PRINT));
            }
            
            $zip->close();
        }

        return $backupPath;
    }

    public function restoreBackup(string $zipPath): bool
    {
        if (!File::exists($zipPath)) {
            return false;
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            DB::beginTransaction();
            try {
                // Disable foreign key checks for the restore process
                $this->disableForeignKeys();

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $tableName = pathinfo($filename, PATHINFO_FILENAME);
                    
                    if (Schema::hasTable($tableName)) {
                        $json = $zip->getFromIndex($i);
                        $data = json_decode($json, true);
                        
                        if (is_array($data)) {
                            DB::table($tableName)->truncate();
                            
                            // Chunk inserts to avoid large payload issues
                            foreach (array_chunk($data, 100) as $chunk) {
                                DB::table($tableName)->insert($chunk);
                            }
                        }
                    }
                }

                $this->enableForeignKeys();
                DB::commit();
                $zip->close();
                return true;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->enableForeignKeys();
                $zip->close();
                throw $e;
            }
        }

        return false;
    }

    protected function disableForeignKeys(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } elseif ($driver === 'pgsql') {
            DB::statement('SET CONSTRAINTS ALL DEFERRED');
        }
    }

    protected function enableForeignKeys(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
