<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class BackupService
{
    /**
     * Create a database backup
     */
    public function createDatabaseBackup($name = null)
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = $name ?: "database_backup_{$timestamp}.sql";
            $filePath = storage_path("app/backups/{$filename}");

            // Ensure backup directory exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Create backup record
            $backup = Backup::create([
                'name' => $name ?: "Database Backup {$timestamp}",
                'type' => 'database',
                'file_path' => $filePath,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // Get database configuration
            $database = config('database.connections.mysql');
            $host = $database['host'];
            $username = $database['username'];
            $password = $database['password'];
            $database_name = $database['database'];

            // Create mysqldump command
            $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database_name} > {$filePath}";

            // Execute backup
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($filePath)) {
                $fileSize = filesize($filePath);
                
                $backup->update([
                    'status' => 'completed',
                    'file_size' => $fileSize,
                    'completed_at' => now(),
                ]);

                return [
                    'success' => true,
                    'backup' => $backup,
                    'message' => 'Database backup created successfully'
                ];
            } else {
                $backup->update([
                    'status' => 'failed',
                    'error_message' => 'Failed to create backup file'
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to create database backup'
                ];
            }

        } catch (\Exception $e) {
            if (isset($backup)) {
                $backup->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }

            return [
                'success' => false,
                'message' => 'Error creating backup: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get backup statistics
     */
    public function getBackupStats()
    {
        $totalBackups = Backup::count();
        $completedBackups = Backup::where('status', 'completed')->count();
        $failedBackups = Backup::where('status', 'failed')->count();
        $lastBackup = Backup::where('status', 'completed')->latest()->first();

        return [
            'total' => $totalBackups,
            'completed' => $completedBackups,
            'failed' => $failedBackups,
            'last_backup' => $lastBackup,
            'last_backup_date' => $lastBackup ? $lastBackup->completed_at->format('Y-m-d H:i:s') : 'Never',
            'last_backup_size' => $lastBackup ? $lastBackup->formatted_size : 'N/A',
        ];
    }

    /**
     * Download backup file
     */
    public function downloadBackup($backupId)
    {
        $backup = Backup::findOrFail($backupId);
        
        if (!$backup->fileExists()) {
            throw new \Exception('Backup file not found');
        }

        return response()->download($backup->file_path, $backup->name . '.sql');
    }

    /**
     * Delete backup
     */
    public function deleteBackup($backupId)
    {
        $backup = Backup::findOrFail($backupId);
        
        if ($backup->fileExists()) {
            unlink($backup->file_path);
        }
        
        $backup->delete();
        
        return [
            'success' => true,
            'message' => 'Backup deleted successfully'
        ];
    }
}
