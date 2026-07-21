<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOptionValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity')->index();
            $table->decimal('price', 15, 2);
            $table->decimal('weight', 15, 2);
            $table->integer('reward');
            $table->string('image', 255);
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('product_option_id')->index();
            $table->unsignedBigInteger('option_id')->index();
            $table->unsignedBigInteger('option_value_id')->index();
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
            $table->foreign('option_value_id')->references('id')->on('option_values')->onDelete('cascade');
            $table->foreign('product_option_id')->references('id')->on('product_option')->onDelete('cascade');
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
        Schema::dropIfExists('product_option_values');
    }
}
