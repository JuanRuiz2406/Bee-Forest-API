<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->uuid('id')->primary(); // llave primaria codigo unico autogenerado para mas seguridad, libreria UUID
            $table->string('name');
            $table->string('surname');
            $table->string('telephone');
            $table->string('direction');
            $table->string('email')->unique();
            $table->date('startDay');
            $table->date('finalDay');
            $table->time('StartTime');
            $table->time('finalTime');
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
        Schema::dropIfExists('providers');
    }
}
