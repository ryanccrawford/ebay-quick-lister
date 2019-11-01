<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SellerItem extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'selleritems';
    
    protected $fillable = [
        'title', 'image','qoh'
    ];
}
