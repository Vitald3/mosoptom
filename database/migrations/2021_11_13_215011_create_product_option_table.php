<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_option', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('required');
            $table->string('value', 255)->nullable();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('option_id')->index();
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('product_option');
    }
}
