<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'db:health-check';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Checks if the database connection is ready';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Coba lakukan query paling ringan
            DB::connection()->getPdo();
            $this->info('Database connection is ready.');
            return 0; // Exit code 0 = Sukses
        } catch (\Exception $e) {
            $this->error('Database connection is not ready yet: ' . $e->getMessage());
            return 1; // Exit code 1 = Gagal
        }
    }
}