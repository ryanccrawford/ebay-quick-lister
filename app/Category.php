<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Catagory extends Eloquent
{
    protected $connection = 'mongodb';

    protected $collection = 'catagories_collection';
}
