<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilterDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filter_description', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 10)->index();
            $table->string('name', 300)->index();
			$table->unique(['lang', 'name']);
            $table->text('description');
            $table->unsignedBigInteger('filter_id')->index();
            $table->foreign('filter_id')->references('id')->on('filters')->onDelete('cascade');
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
        Schema::dropIfExists('filter_description');
    }
}
