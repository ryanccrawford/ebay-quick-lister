<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SellerItem', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',80);
            $table->integer('qty');
            $table->double('price', 10, 2);
            $table->string('sku');
            $table->longText('descriptionEditorArea');
            $table->integer('ShippingPoliciesResponse' );
            $table->integer('ReturnPoliciesResponse' );
            $table->integer('PaymentPoliciesResponse');
            $table->double('shippingCost', 10, 2);
            $table->integer('shippingLength' );
            $table->integer('shippingWidth');
            $table->integer('shippingHeight');
            $table->integer('shippingWeight');
            $table->integer('primaryCategory');
            $table->string('descriptionImageFile');
            $table->string('mainImageFile');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('SellerItem');
    }
}
