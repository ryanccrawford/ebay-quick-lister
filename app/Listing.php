<?php

namespace App;

use DTS\eBaySDK\Inventory\Types\InventoryItem;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{

    public $provider;
    public $inventoryService;


    public function constuctor()
    {
        //
    }
}
