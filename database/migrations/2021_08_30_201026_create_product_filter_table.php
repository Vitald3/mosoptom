<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFilterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_filter', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('filter_id')->index();
            $table->unsignedBigInteger('filter_value_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unique(['filter_id', 'filter_value_id', 'product_id']);
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
			$table->foreign('filter_id')->references('id')->on('filters')->onDelete('cascade');
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
        Schema::dropIfExists('product_filter');
    }
}
