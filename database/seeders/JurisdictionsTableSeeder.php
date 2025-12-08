<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jurisdiction;
use App\Models\County;



class JurisdictionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $county = County::where('name', 'Los Angeles County')->firstOrFail();

        Jurisdiction::create(['name' => 'Unincorporated', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Acton', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Agoura Hills', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Agua Dulce', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Alhambra', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Altadena', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Arcadia', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Artesia', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Athens', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Avalon', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Azusa', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Baldwin Park', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Bassett', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Bell', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Bell Canyon', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Bell Gardens', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Bellflower', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Beverly Hills', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Box Canyon', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Bradbury', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Burbank', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Calabasas', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Calabasas Hills', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Canoga Park', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Canyon Country', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Carson', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Castaic', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Cerritos', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Chatsworth', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'City Of Commerce', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'City Of Industry', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'City Ranch', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Claremont', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Compton', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Cornell', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Covina', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Crystalaire', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Cudahy', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Culver City', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Del Sur', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Diamond Bar', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Downey', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Duarte', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'East Los Angeles', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'East Rancho Dominguez', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'El Monte', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'El Segundo', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Elizabeth Lake', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Encino', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Firestone Park', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Florence-Graham', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Gardena', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Glendale', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Glendora', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Granada Hills', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Hawthorne', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Huntington Park', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Inglewood', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'La Canada Flintridge', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'La Crescenta', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'La Mirada', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'La Puente', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'La Verne', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Lakewood', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Lancaster', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Lawndale', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Lennox', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Los Angeles', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Long Beach', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Lynwood', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Malibu', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Manhattan Beach', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Marina del Rey', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Monrovia', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Montebello', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Monterey Park', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Norwalk', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Palmdale', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Palos Verdes Estates', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Pico Rivera', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Pomona', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Rancho Palos Verdes', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Redondo Beach', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Rolling Hills', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Rolling Hills Estates', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'San Fernando', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'San Gabriel', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'San Marino', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Santa Clarita', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Santa Fe Springs', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Santa Monica', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Sierra Madre', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'South El Monte', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'South Gate', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'South Pasadena', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Temple City', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Torrance', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Valencia', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Vernon', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Walnut', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'West Covina', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'West Hollywood', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Westlake Village', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Whittier', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Wilmington', 'county_id' => $county->id]);
        Jurisdiction::create(['name' => 'Woodland Hills', 'county_id' => $county->id]);
    }
}
