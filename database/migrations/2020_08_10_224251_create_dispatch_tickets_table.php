<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatch_tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('orderId')->unsigned();
            $table->double('totalPrice');
            $table->date('dispatchDate');
            $table->string('status');
            $table->integer('discount');
            $table->timestamps();

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
        Schema::dropIfExists('dispatch_tickets');
    }
}
