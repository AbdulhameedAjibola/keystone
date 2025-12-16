<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Agent;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory()->
        count(5)->
        state(['role'=> 'admin'])->
        hasCareers(5)->
        create();

        User::factory()
        ->count(20)
        ->hasInquiries(2)
        ->create();

        Agent::factory()->count(20)->hasProperty(
            Property::factory()->count(5)->hasMedia(5)
        )->create();
     
        
    }
}
