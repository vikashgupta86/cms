<?php

namespace App\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;

class BackupController extends Controller
{
    use Authorizable;

    public $module_title;

    public $module_name;

    public $module_path;

    public $module_icon;

    public $module_model;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Backup';

        // module name
        $this->module_name = 'backups';

        // directory path of the module
        $this->module_path = 'backups';

        // module icon
        $this->module_icon = 'fas fa-archive';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';

        $disk = Storage::disk('local');

        $files = $disk->files(config('backup.backup.name'));

        $$module_name = [];

        // make an array of backup files, with their filesize and creation date
        foreach ($files as $k => $f) {
            // only take the zip files into account
            if (substr($f, -4) === '.zip' && $disk->exists($f)) {
                $$module_name[] = [
                    'file_path' => $f,
                    'file_name' => str_replace(config('backup.backup.name').'/', '', $f),
                    'file_size_byte' => $disk->size($f),
                    'file_size' => humanFilesize($disk->size($f)),
                    'last_modified_timestamp' => $disk->lastModified($f),
                    'date_created' => Carbon::createFromTimestamp($disk->lastModified($f))->isoFormat('llll'),
                    'date_ago' => Carbon::createFromTimestamp($disk->lastModified($f))->diffForHumans(Carbon::now()),
                ];
            }
        }

        // reverse the backups, so the newest one would be on top
        $$module_name = array_reverse($$module_name);

        logUserAccess($module_title.' '.$module_action);

        return view(
            "backend.{$module_path}.backups",
            compact('module_title', 'module_name', "{$module_name}", 'module_path', 'module_icon', 'module_action', 'module_name_singular')
        );
    }

    /**
     * Creates a new backup for the module.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Create';

        if (demo_mode()) {
            flash('Backup Creation Skillped on Demo Mode!')->warning()->important();

            logUserAccess($module_title.' '.$module_action);

            return redirect()->route("backend.{$module_path}.index");
        }

        try {
            // start the backup process
            Artisan::call('backup:run');
            $output = Artisan::output();

            // Log the results
            Log::info("Backpack\BackupManager -- new backup started from admin interface \r\n".$output);

            logUserAccess($module_title.' '.$module_action);

            // return the results as a response to the ajax call
            flash('New backup created')->success()->important();

            return redirect()->back();
        } catch (Exception $e) {
            Flash::error($e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Run a custom SQL backup for the current database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function backup()
    {
        $defaultConnection = config('database.default');

        if ($defaultConnection === 'mysql') {
            return $this->backupMySQL();
        } elseif ($defaultConnection === 'sqlite') {
            return $this->backupSQLite();
        } else {
            return back()->with('error', 'Database backup currently supports only MySQL and SQLite.');
        }
    }

    private function backupMySQL()
    {
        $backupDir = base_path('database/backups');
        File::ensureDirectoryExists($backupDir);

        $filename = sprintf('backup-%s.sql', now()->format('Ymd-His'));
        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        try {
            $pdo = DB::connection()->getPdo();
            $database = config('database.connections.' . config('database.default') . '.database');

            $sql = "-- Database Backup: {$database}\n";
            $sql .= "-- Generated: " . now()->toDateTimeString() . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createStmt['Create Table'] . ";\n\n";

                $offset = 0;
                $chunkSize = 500;

                while (true) {
                    $rows = $pdo->query("SELECT * FROM `{$table}` LIMIT {$chunkSize} OFFSET {$offset}")->fetchAll(\PDO::FETCH_ASSOC);

                    if (empty($rows)) {
                        break;
                    }

                    $columns = '`' . implode('`, `', array_keys($rows[0])) . '`';
                    $values = [];

                    foreach ($rows as $row) {
                        $escaped = array_map(function ($val) use ($pdo) {
                            return is_null($val) ? 'NULL' : $pdo->quote($val);
                        }, array_values($row));
                        $values[] = '(' . implode(', ', $escaped) . ')';
                    }

                    $sql .= "INSERT INTO `{$table}` ({$columns}) VALUES\n";
                    $sql .= implode(",\n", $values) . ";\n\n";

                    $offset += $chunkSize;
                }
            }

            $views = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($views as $view) {
                $createStmt = $pdo->query("SHOW CREATE VIEW `{$view}`")->fetch(\PDO::FETCH_ASSOC);
                $sql .= "DROP VIEW IF EXISTS `{$view}`;\n";
                $sql .= $createStmt['Create View'] . ";\n\n";
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put($backupPath, $sql);
        } catch (\Throwable $e) {
            Log::error('Database backup error', ['exception' => $e->getMessage()]);

            return back()->with('error', 'Database backup encountered an error.');
        }

        return back()->with('success', "Database backup saved to database/backups/{$filename}");
    }

    private function backupSQLite()
    {
        $backupDir = base_path('database/backups');
        File::ensureDirectoryExists($backupDir);

        $databasePath = config('database.connections.sqlite.database');
        $filename = sprintf('backup-%s.sqlite', now()->format('Ymd-His'));
        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        try {
            if (!File::exists($databasePath)) {
                return back()->with('error', 'SQLite database file not found.');
            }

            File::copy($databasePath, $backupPath);
        } catch (\Throwable $e) {
            Log::error('SQLite backup error', ['exception' => $e->getMessage()]);

            return back()->with('error', 'SQLite backup encountered an error.');
        }

        return back()->with('success', "SQLite database backup saved to database/backups/{$filename}");
    }

    private function findMysqlDumpBinary(): ?string
    {
        $candidates = [];

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $candidates[] = 'mysqldump.exe';
            $candidates[] = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
            $candidates[] = 'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe';
            $candidates[] = 'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe';
        } else {
            $candidates[] = 'mysqldump';
        }

        foreach ($candidates as $candidate) {
            if (File::exists($candidate) || $this->commandExists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function commandExists(string $command): bool
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = null;
            $status = null;
            exec("where {$command} 2>NUL", $output, $status);
            return $status === 0 && ! empty($output);
        }

        $output = null;
        $status = null;
        exec("command -v {$command} 2>/dev/null", $output, $status);
        return $status === 0 && ! empty($output);
    }

    /**
     * Downloads a backup zip file.
     *
     * TODO: make it work no matter the flysystem driver (S3 Bucket, etc).
     */
    public function download($file_name)
    {
        $disk = Storage::disk('local');
        $file = config('backup.backup.name').'/'.$file_name;

        if ($disk->exists($file)) {
            logUserAccess(__METHOD__.' | Downloaded backup file: '.$file_name);

            return Storage::download($file);
        }

        logUserAccess(__METHOD__.' | Failed to download backup file: '.$file_name);

        abort(404, "The backup file doesn't exist.");
    }

    /**
     * Deletes a backup file.
     */
    public function delete($file_name)
    {
        $disk = Storage::disk('local');
        $file = config('backup.backup.name').'/'.$file_name;

        if ($disk->exists($file)) {
            logUserAccess(__METHOD__.' | Deleting backup file: '.$file_name);

            $disk->delete($file);

            flash("`{$file_name}` deleted successfully.")->success()->important();

            logUserAccess(__METHOD__.' | Deleted backup file: '.$file_name);

            return redirect()->back();
        }

        logUserAccess(__METHOD__.' | Failed to delete backup file: '.$file_name);

        abort(404, "The backup file doesn't exist.");
    }
}
