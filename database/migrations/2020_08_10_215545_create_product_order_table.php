<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_order', function (Blueprint $table) { // Detalle de pedidos
            $table->id();
            $table->bigInteger('productId')->unsigned();
            $table->bigInteger('orderId')->unsigned();
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('productId')->references('id')->on('products');
            $table->foreign('orderId')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_order');
    }
}
