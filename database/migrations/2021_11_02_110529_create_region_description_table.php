<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('region_description', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('region_id')->index();
            $table->string('lang', 300)->index();
            $table->string('name', 300)->index();
            $table->string('format1', 300);
            $table->string('format2', 300);
            $table->string('format3', 300);
            $table->string('meta_title', 300);
            $table->string('meta_description', 300);
            $table->string('meta_keywords', 300);
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
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
        Schema::dropIfExists('region_description');
    }
}
