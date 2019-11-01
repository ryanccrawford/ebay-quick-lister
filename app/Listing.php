<?php

namespace App;

use DTS\eBaySDK\Inventory\Types\InventoryItem;


class Listing extends Moloquent
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
