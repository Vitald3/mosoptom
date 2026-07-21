<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilterValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filter_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sort')->index();
			$table->integer('top')->index();
            $table->string('slug')->index()->unique();
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
        Schema::dropIfExists('filter_values');
    }
}
