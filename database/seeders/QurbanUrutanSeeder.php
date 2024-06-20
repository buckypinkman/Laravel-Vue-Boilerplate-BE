<?php

namespace Database\Seeders;

use App\Models\QurbanUrutan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QurbanUrutanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [];

        for ($i = 1; $i <= 40; $i++) {
            $items[] = [
                'hewan' => 'sapi',
                'name' => 'Sapi ' . $i,
                'created_at' => now()
            ];
        }

        for ($i = 1; $i <= 70; $i++) {
            $items[] = [
                'hewan' => 'kambing',
                'name' => 'Kambing ' . $i,
                'created_at' => now()
            ];
        }

        (new QurbanUrutan())->delete();

        QurbanUrutan::insert($items);
    }
}
