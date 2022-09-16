<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('setting')->insert([
            'id_setting' => 1,
            'nama_perusahaan' => 'Sumber Rejeki Ban',
            'alamat' => 'Jl. Peta Barat No.24, Jurumudi, Benda, Kota Tangerang, Banten 15124',
            'telepon' => '0215500759',
            'tipe_nota' => 1, // kecil
            'diskon' => 0,
            'path_logo' => '/img/logo.png',
            'path_kartu_member' => '/img/member.png',
        ]);
    }
}
