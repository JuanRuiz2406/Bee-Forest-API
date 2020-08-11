<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRefundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_refund', function (Blueprint $table) { //Detalle de devolucion
            $table->id();
            $table->bigInteger('productId')->unsigned();
            $table->bigInteger('refundId')->unsigned();
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('productId')->references('id')->on('products');
            $table->foreign('refundId')->references('id')->on('refunds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_refund');
    }
}
