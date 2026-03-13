<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:fix-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix imported user passwords that are invalid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for users with invalid passwords...');

        // Find users whose password starts with the dummy hash pattern or is not a valid hash
        // The problematic hash seen in screenshot starts with $2y$12$DefAuLtPaSsWoRdHaSh
        $users = User::where('password', 'like', '$2y$12$DefAuLtPaSsWoRdHaSh%')->get();
        
        $count = $users->count();

        if ($count === 0) {
            $this->info('No users found with the specific invalid password pattern.');
            
            if ($this->confirm('Do you want to check for ALL users and reset passwords for those with potentially invalid hashes?')) {
                $users = User::all();
                $count = 0;
                foreach ($users as $user) {
                    // Check if password is valid bcrypt
                    $info = password_get_info($user->password);
                    if ($info['algoName'] !== 'bcrypt') {
                        $user->password = Hash::make('password');
                        $user->save();
                        $this->line("Reset password for: {$user->email}");
                        $count++;
                    }
                }
                $this->info("Reset passwords for $count users.");
            }
            return;
        }

        $this->info("Found {$count} users with invalid passwords.");

        if ($this->confirm("Do you want to reset their passwords to 'password'?", true)) {
            $bar = $this->output->createProgressBar($count);
            $bar->start();

            foreach ($users as $user) {
                $user->password = Hash::make('password');
                $user->save();
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Passwords updated successfully!');
        }
    }
}
