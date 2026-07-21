<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilterValueDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filter_value_description', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 10)->index();
            $table->string('name', 300)->index();
            $table->unique(['lang', 'name', 'filter_value_id']);
            $table->unsignedBigInteger('filter_value_id')->index();
            $table->foreign('filter_value_id')->references('id')->on('filter_values')->onDelete('cascade');
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
        Schema::dropIfExists('filter_value_description');
    }
}
