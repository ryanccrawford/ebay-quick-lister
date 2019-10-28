<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ebaytest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->env('EBAY_PROD_APP_ID',);

        $response->assert('RyanCraw-7191-4ec8-b38a-6e7ab0cf5091', 'Is this',);
    }
}
