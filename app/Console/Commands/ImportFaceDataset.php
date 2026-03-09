<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ImportFaceDataset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'face:import-dataset {path : The absolute path to the dataset folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import face dataset using Node.js worker for high performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');

        if (!is_dir($path)) {
            $this->error("Directory not found: $path");
            return 1;
        }

        $scriptPath = base_path('scripts/face-worker/index.js');
        
        if (!file_exists($scriptPath)) {
            $this->error("Worker script not found at: $scriptPath");
            $this->info("Please run 'npm install' inside 'scripts/face-worker' directory first.");
            return 1;
        }

        $this->info("🚀 Starting Node.js Face Worker...");
        $this->info("📂 Dataset: $path");
        
        // Command: node scripts/face-worker/index.js "C:/Path/To/Dataset"
        $process = new Process(['node', $scriptPath, $path]);
        $process->setTimeout(3600); // 1 hour timeout
        $process->setIdleTimeout(300); // 5 minutes idle timeout
        
        $process->run(function ($type, $buffer) {
            // Output streaming
            $this->output->write($buffer);
        });

        if ($process->isSuccessful()) {
            $this->info('✅ Face import completed successfully!');
            return 0;
        } else {
            $this->error('❌ Face import failed.');
            return 1;
        }
    }
}
