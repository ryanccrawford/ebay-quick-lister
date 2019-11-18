<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'name' => 'EBAY_PROD_APP_ID',
            'value' => ''
        ]);

        DB::table('settings')->insert([
            'name' => 'EBAY_PROD_DEV_ID',
            'value' => ''
        ]);

        DB::table('settings')->insert([
            'name' => 'EBAY_PROD_CERT_ID',
            'value' => ''
        ]);

        DB::table('settings')->insert([
            'name' => 'EBAY_PROD_RUNAME',
            'value' => ''
        ]);

    }
}
