<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Camps;
use Illuminate\Database\Seeder;

class CampTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $camps = [
            [
                'title' =>  'Gila Belajar',
                'slug'  =>  'gila-belajar',
                'price' =>  '280',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ],

            [
                'title' =>  'Baru Muali',
                'slug'  =>  'baru-mulai',
                'price' =>  '140',
                'created_at'    =>  now(),
                'updated_at'    =>  now(),

            ]
        ];


        // 1st Method
        // foreach ($camps as $key => $camp) {
        //     Camps::create($camp);
        // }

        // 2nd Method
        Camps::insert($camps);
    }
}
