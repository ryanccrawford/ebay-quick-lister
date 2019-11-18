<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    public function constuctor()
    {
        
    }

	protected $fillable = [
		'name', 'value'
	];
}
