<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_option', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('option_id')->index();
            $table->unsignedBigInteger('order_product_id')->index();
            $table->unsignedBigInteger('product_option_id')->index();
            $table->unsignedBigInteger('product_option_value_id')->index()->nullable();
            $table->string('type', 55);
            $table->string('name', 255);
            $table->string('value', 64);
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
            $table->foreign('order_product_id')->references('id')->on('order_product')->onDelete('cascade');
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
        Schema::dropIfExists('order_option');
    }
}
