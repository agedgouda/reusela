<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jurisdiction;
use App\Models\County;

class DefaultJurisdictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $county = County::where('name', 'Los Angeles County')->firstOrFail();

        Jurisdiction::create([
            'name' => 'System Default',
            'is_system_default' => true,
            'general_information' => 'Initial Default Content',
            'county_id' => $county->id
        ]);
    }
}
