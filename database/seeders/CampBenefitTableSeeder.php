<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CampBenefit;

class CampBenefitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $camp_benefits = [
            ['camp_id' => 1, 'name' => 'Pro Techstack Kit'],
            ['camp_id' => 1, 'name' => 'iMac Pro 2021 & Display'],
            ['camp_id' => 1, 'name' => '1-1 Mentoring Program'],
            ['camp_id' => 1, 'name' => 'Final Project Sertificate'],
            ['camp_id' => 1, 'name' => 'Offile Course Video'],
            ['camp_id' => 1, 'name' => 'Premium Design Kit'],
            ['camp_id' => 1, 'name' => 'Website Builder'],

            ['camp_id' => 2, 'name' => '1-1 Mentoring Program'],
            ['camp_id' => 2, 'name' => 'Final Project Sertificate'],
            ['camp_id' => 2, 'name' => 'Offile Course Video'],
            ['camp_id' => 2, 'name' => 'Premium Design Kit'],
        ];

        CampBenefit::insert($camp_benefits);
    }
}
