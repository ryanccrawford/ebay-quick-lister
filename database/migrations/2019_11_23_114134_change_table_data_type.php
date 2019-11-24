<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTableDataType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('SellerItem', function (Blueprint $table) {
            $table->bigInteger('ShippingPoliciesResponse')->change();
            $table->bigInteger('ReturnPoliciesResponse')->change();
            $table->bigInteger('PaymentPoliciesResponse')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('SellerItem', function (Blueprint $table) {
            //
        });
    }
}
