<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SellerItem extends Model
{

    protected $connection = 'mysql';
    protected $table = 'selleritem';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'shippingWidth' => 10,
        'shippingLength' => 10,
        'shippingHeight' => 10,
        'shippingCost' => 0.00,
        'ebayItemId' => '',
    ];

    protected $fillable = [
        'title',
        'price',
        'sku',
        'descriptionEditorArea',
        'ShippingPoliciesResponse',
        'ReturnPoliciesResponse',
        'PaymentPoliciesResponse',
        'shippingCost',
        'shippingLength',
        'shippingWidth',
        'shippingHeight',
        'shippingWeight',
        'qty',
        'primaryCategory',
        'descriptionImageFile',
        'mainImageFile'
    ];
}
