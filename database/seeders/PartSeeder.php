<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\PartGroup;
use Illuminate\Database\Seeder;

class PartSeeder extends Seeder
{
    public function run(): void
    {
        // Buat groups dulu
        $groups = [
            'HGP', 'HGA',
        ];

        foreach ($groups as $name) {
            PartGroup::firstOrCreate(['name' => $name]);
        }

        $parts = [
            ['kode_part' => 'BP-001', 'deskripsi_part' => 'Brake Pad Front',      'group' => 'HGP'],
            ['kode_part' => 'BP-002', 'deskripsi_part' => 'Brake Pad Rear',       'group' => 'HGP'],
            ['kode_part' => 'SK-001', 'deskripsi_part' => 'Shock Absorber Front', 'group' => 'HGA'],
            ['kode_part' => 'SK-002', 'deskripsi_part' => 'Shock Absorber Rear',  'group' => 'HGA'],
            ['kode_part' => 'FR-001', 'deskripsi_part' => 'Air Filter',           'group' => 'HGA'],
            ['kode_part' => 'FR-002', 'deskripsi_part' => 'Oil Filter',           'group' => 'HGA'],
            ['kode_part' => 'OL-001', 'deskripsi_part' => 'Engine Oil 10W-40',    'group' => 'HGP'],
            ['kode_part' => 'OL-002', 'deskripsi_part' => 'Gear Oil',             'group' => 'HGP'],
            ['kode_part' => 'SP-001', 'deskripsi_part' => 'Spark Plug Standard',  'group' => 'HGP'],
            ['kode_part' => 'SP-002', 'deskripsi_part' => 'Spark Plug Iridium',   'group' => 'HGA'],
        ];

        foreach ($parts as $data) {
            $group = PartGroup::where('name', $data['group'])->first();
            Part::firstOrCreate(
                ['kode_part' => $data['kode_part']],
                [
                    'deskripsi_part' => $data['deskripsi_part'],
                    'part_group_id'  => $group->id,
                ]
            );
        }
    }
}