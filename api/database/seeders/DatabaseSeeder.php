<?php

namespace Database\Seeders;

use App\Models\Email;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Email::factory()->count(10)->create();
    }
}
