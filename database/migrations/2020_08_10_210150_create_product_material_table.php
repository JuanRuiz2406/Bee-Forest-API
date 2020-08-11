<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Tabla con relacion de muchos a muchos (detalles) tambien llamadas tablas pivotes
        Schema::create('product_material', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('productId')->unsigned();
            $table->bigInteger('materialId')->unsigned();
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('productId')->references('id')->on('products');
            $table->foreign('materialId')->references('id')->on('materials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_material');
    }
}
