<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SellerItem extends Model
{
 
    protected $connection = 'mysql';
    
    protected $fillable = [
        'title', 
        'price',
        'sku',
        'descriptionEditorArea',
        'ShippingPoliciesResponse' ,
        'ReturnPoliciesResponse' ,
        'PaymentPoliciesResponse',
        'shippingCost',
        'shippingLength' ,
        'shippingWidth',
        'shippingHeight',
        'shippingWeight',
        'qty',
        'primaryCategory',
        'descriptionImageFile',
        'mainImageFile'
    ];
}
