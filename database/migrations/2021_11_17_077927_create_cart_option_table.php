<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_option', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cart_id')->index();
            $table->unsignedBigInteger('option_id')->index();
            $table->unsignedBigInteger('product_option_id')->index();
            $table->unsignedBigInteger('product_option_value_id')->index()->nullable();
            $table->string('value', 255)->nullable();
            $table->foreign('cart_id')->references('id')->on('cart')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
            $table->foreign('product_option_id')->references('id')->on('product_option')->onDelete('cascade');
            $table->foreign('product_option_value_id')->references('id')->on('product_option_values')->onDelete('cascade');
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
        Schema::dropIfExists('cart_option');
    }
}
