<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('donations')->insert([
            [
                'type'            => 'cash',
                'amount'          => 1040.00,
                'description'     => 'Support for cleanup project',
                'donor_name'      => 'Alice',
                'donor_email'     => 'alice@example.com',
                'method'          => 'Stripe',
                'transaction_id'  => 'TXN123456',
                'payment_status'  => 'pending',
                'date'            => now(),
                'project_id'      => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'type'            => 'cash',
                'amount'          => 1300.00,
                'description'     => 'Support for cleanup project',
                'donor_name'      => 'Alice',
                'donor_email'     => 'alice@example.com',
                'method'          => 'Stripe',
                'transaction_id'  => 'TXN123456',
                'payment_status'  => 'paid',
                'date'            => now(),
                'project_id'      => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

            [
                'type'            => 'cash',
                'amount'          => 1000.00,
                'description'     => '<Supp></Supp>ort for cleanup project',
                'donor_name'      => 'Alice',
                'donor_email'     => 'alice@example.com',
                'method'          => 'Stripe',
                'transaction_id'  => 'TXN123456',
                'payment_status'  => 'failed',
                'date'            => now(),
                'project_id'      => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

        ]);
    }
}
