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
            'name' => 'appId',
            'groupName' => 'ebay',
            'value' => env('EBAY_PROD_APP_ID'),
        ]);

        DB::table('settings')->insert([
            'name' => 'devId',
            'groupName' => 'ebay',
            'value' => env('EBAY_PROD_DEV_ID')
        ]);

        DB::table('settings')->insert([
            'name' => 'certId',
            'groupName' => 'ebay',
            'value' => env('EBAY_PROD_CERT_ID')
        ]);

        DB::table('settings')->insert([
            'name' => 'ruName',
            'groupName' => 'ebay',
            'value' =>  env('EBAY_PROD_RUNAME')
        ]);

        DB::table('settings')->insert([
            'name' => 'authToken',
            'groupName' => 'ebay',
            'value' =>  env('EBAY_PROD_AUTH_TOKEN')
        ]);

        DB::table('settings')->insert([
            'name' => 'ebayMode',
            'groupName' => 'ebay',
            'value' =>  'production'
        ]);

        DB::table('settings')->insert([
            'name' => 'siteId',
            'groupName' => 'ebay',
            'value' =>  '0'
        ]);
    }
}
