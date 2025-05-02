<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            [
                'name' => 'PT Telkom Indonesia',
                'address' => 'Jl. Japati No.1, Bandung, Jawa Barat',
                'contact_person' => 'Ahmad Fauzi',
                'contact_email' => 'ahmad.fauzi@telkom.co.id',
                'contact_phone' => '022-1234567',
                'industry_field' => 'Telekomunikasi dan Teknologi Informasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Gojek Indonesia',
                'address' => 'Pasaraya Blok M, Jakarta Selatan',
                'contact_person' => 'Nadiem Makarim',
                'contact_email' => 'nadiem@gojek.com',
                'contact_phone' => '021-7654321',
                'industry_field' => 'Transportasi dan Teknologi Informasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Bukalapak',
                'address' => 'Jl. Kemang Timur No.22, Jakarta Selatan',
                'contact_person' => 'Achmad Zaky',
                'contact_email' => 'achmad.zaky@bukalapak.com',
                'contact_phone' => '021-9876543',
                'industry_field' => 'E-commerce dan Teknologi Informasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Tokopedia',
                'address' => 'Jl. Karet Pasar Baru Barat No.5, Jakarta Selatan',
                'contact_person' => 'William Tanuwijaya',
                'contact_email' => 'william@tokopedia.com',
                'contact_phone' => '021-8765432',
                'industry_field' => 'E-commerce dan Teknologi Informasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Traveloka Indonesia',
                'address' => 'Jl. Kebon Sirih No.17-19, Jakarta Pusat',
                'contact_person' => 'Ferry Unardi',
                'contact_email' => 'ferry.unardi@traveloka.com',
                'contact_phone' => '021-6543210',
                'industry_field' => 'Travel dan Teknologi Informasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}