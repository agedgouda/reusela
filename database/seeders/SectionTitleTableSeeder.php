<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SectionTitle;

class SectionTitleTableSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            'Single-use Foodware Accessories (Utensils, Straws, Stirrers, etc.)',
            'Allowable & Prohibited Foodware',
            'Reuse for Dine-In',
            'Single-Use Beverage Containers',
            'Polystyrene (“Styrofoam®”) Ban',
            'Carry-Out Bag Rules And Fees',
            'Balloons',
            'Other*',
            'Recycling & Organics Requirements',
            'Enforcement & Penalties',
            '“At a Glance” – What this Means for Your Business',
            'Report a Violation',
            'Statewide Laws You Should Know About',
        ];

        foreach ($titles as $index => $title) {
            SectionTitle::create([
                'title' => $title,
                'sort_order' => $index + 1, // ← ADDED
            ]);
        }
    }
}
