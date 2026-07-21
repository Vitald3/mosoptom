<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionValueDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_value_description', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64)->index();
            $table->string('lang', 10)->index();
            $table->unsignedBigInteger('option_value_id')->index();
            $table->foreign('option_value_id')->references('id')->on('option_values')->onDelete('cascade');
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
        Schema::dropIfExists('option_value_description');
    }
}
