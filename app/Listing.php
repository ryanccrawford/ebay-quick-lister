<?php

namespace App;

use DTS\eBaySDK\Credentials\CredentialsProvider;
use DTS\eBaySDK\inventory\Services;
use DTS\eBaySDK\Inventory\Services\InventoryService;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{

    public $provider;
    public $inventoryService;


    public function constuctor()
    {

        $this->provider = CredentialsProvider::ini();
        $this->provider = CredentialsProvider::memoize($this->provider);
        $sdk = new DTS\eBaySDK\Sdk(
            [
                'globalId'   => 'EBAY-US',
            ]
        );
        $this->inventoryService = $sdk->createInventory(
            [
                'credentials' => $this->provider,
            ]
        );
        $request = new DTS\eBaySDK\Inventory\GetInventoryItemRestRequest();
    }
}
