<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_description', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64)->index();
            $table->string('lang', 10)->index();
            $table->unsignedBigInteger('option_id')->index();
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
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
        Schema::dropIfExists('option_description');
    }
}
