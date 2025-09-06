<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RunSqlScript extends Command
{
    protected $signature = 'db:run-sql {file : Relative path to the .sql file}';
    protected $description = 'Execute a raw SQL script file';

    public function handle()
    {
        $filePath = base_path($this->argument('file'));

        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $sql = File::get($filePath);

        try {
            DB::unprepared($sql);
            $this->info("✅ SQL script executed successfully.");
        } catch (\Exception $e) {
            $this->error("❌ Error executing SQL: " . $e->getMessage());
        }

        return 0;
    }
}
