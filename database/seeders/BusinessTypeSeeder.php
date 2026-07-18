<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Duka la Jumla', 'slug' => 'retail', 'icon' => 'shopping-bag', 'description' => 'Maduka ya rejareja — retail shops', 'sort_order' => 1],
            ['name' => 'Mwaguaji', 'slug' => 'wholesale', 'icon' => 'truck', 'description' => 'Mauzo ya jumla — wholesale business', 'sort_order' => 2],
            ['name' => 'Duka la Dawa', 'slug' => 'pharmacy', 'icon' => 'beaker', 'description' => 'Maduka ya dawa — pharmacies', 'sort_order' => 3],
            ['name' => 'Duka la Vifaa', 'slug' => 'hardware', 'icon' => 'wrench', 'description' => 'Maduka ya vifaa — hardware stores', 'sort_order' => 4],
            ['name' => 'Supamaketi', 'slug' => 'supermarket', 'icon' => 'cart', 'description' => 'Supamaketi — supermarkets', 'sort_order' => 5],
            ['name' => 'Mkahawa', 'slug' => 'restaurant', 'icon' => 'fire', 'description' => 'Mikahawa na migahawa — restaurants', 'sort_order' => 6],
            ['name' => 'Saluni ya Macho', 'slug' => 'optical', 'icon' => 'eye', 'description' => 'Saluni za macho — optical shops', 'sort_order' => 7],
            ['name' => 'Maduka ya Simu', 'slug' => 'electronics', 'icon' => 'device-mobile', 'description' => 'Maduka ya simu na elektroniki — electronics', 'sort_order' => 8],
            ['name' => 'Mavazi na Nguo', 'slug' => 'fashion', 'icon' => 'sparkles', 'description' => 'Maduka ya mavazi — fashion & clothing', 'sort_order' => 9],
            ['name' => 'Vifaa wa Nyumbani', 'slug' => 'home_goods', 'icon' => 'home', 'description' => 'Vifaa vya nyumbani — home goods', 'sort_order' => 10],
            ['name' => 'Kituo cha Huduma', 'slug' => 'service', 'icon' => 'briefcase', 'description' => 'Vituo vya huduma — service centers', 'sort_order' => 11],
            ['name' => 'Nyingine', 'slug' => 'other', 'icon' => 'dots-horizontal', 'description' => 'Aina nyingine za biashara', 'sort_order' => 99],
        ];

        foreach ($types as $type) {
            BusinessType::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
