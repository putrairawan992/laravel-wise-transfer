<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin', 
                'password' => bcrypt('password'), 
                'role' => 'admin'
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'daniel@example.com'],
            [
                'name' => 'Ayobami Daniel', 
                'password' => bcrypt('password'), 
                'role' => 'user'
            ]
        );

        $account = Account::query()->updateOrCreate(
            ['user_id' => $user->id, 'currency' => 'NGN'],
            [
                'display_name' => 'Main Wallet',
                'balance' => 50000.00,
                'bank_name' => 'Wise Bank',
                'bank_code' => 'WB-001',
                'account_number' => '1234567890',
            ]
        );

        // Seed some transfers
        Transfer::query()->updateOrCreate([
            'user_id' => $user->id,
            'order_number' => '111',
        ],[
            'user_id' => $user->id,
            'account_id' => $account->id,
            'amount' => 750.00,
            'currency' => 'NGN',
            'merchant' => 'John Doe',
            'method' => 'Wallet',
            'order_number' => '111',
            'fee' => 0.15,
            'total' => 750.15,
            'recipient_name' => 'John Doe',
            'recipient_account_mask' => '****1234',
            'recipient_account' => '12345678901234',
            'status' => 'success',
            'idempotency_key' => Str::uuid()->toString(),
        ]);
        
        Transfer::query()->updateOrCreate([
            'user_id' => $user->id,
            'order_number' => '123',
        ],[
            'user_id' => $user->id,
            'account_id' => $account->id,
            'amount' => 2000.00,
            'currency' => 'NGN',
            'merchant' => 'Jane Smith',
            'method' => 'Wallet',
            'order_number' => '123',
            'fee' => 0.03,
            'total' => 2000.03,
            'recipient_name' => 'Jane Smith',
            'recipient_account_mask' => '****5678',
            'recipient_account' => '98765432105678',
            'status' => 'success',
            'idempotency_key' => Str::uuid()->toString(),
        ]);
    }
}
