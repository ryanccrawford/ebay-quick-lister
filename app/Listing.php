<?php

namespace App;

use DTS\eBaySDK\Inventory\Types\InventoryItem;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Listing extends Eloquent
{

    public $provider;
    public $inventoryService;
    protected $connection = 'mongodb';
    protected $collection = 'listings';

    public function constuctor()
    {
        //
    }
}
