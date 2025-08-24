<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('expenses')->insert([
            [
                'title'       => 'Cleaning Supplies',
                'description' => 'Brooms, gloves, and bags.',
                'amount'      => 50.00,
                'date'        => now(),
                'project_id'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
