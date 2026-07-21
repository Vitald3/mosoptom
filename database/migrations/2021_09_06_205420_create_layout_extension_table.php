<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayoutExtensionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layout_extension', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('extension_id')->nullable();
            $table->string('code')->index();
            $table->string('position')->index();
            $table->unsignedBigInteger('layout_id');
            $table->integer('sort')->index();
            $table->foreign('layout_id')->references('id')->on('layouts')->onDelete('cascade');
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
        Schema::dropIfExists('layout_extension');
    }
}
