<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('collaboratorId');
            $table->uuid('clientId');
            $table->bigInteger('ShippingId')->unsigned();
            $table->datetime('creationDate');
            $table->datetime('deliveryDate');
            $table->double('totalPrice');
            $table->string('status');
            $table->timestamps();

            $table->foreign('collaboratorId')->references('id')->on('collaborators');
            $table->foreign('clientId')->references('id')->on('clients');
            $table->foreign('ShippingId')->references('id')->on('shippings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
