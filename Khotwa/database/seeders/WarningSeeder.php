<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarningSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('warnings')->insert([
            [
                'volunteer_id' => 1,
                'event_id'     => 1,
                'supervisor_id'=> 2,
                'reason'       => 'Late arrival',
                'status'       => 'Pending',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
