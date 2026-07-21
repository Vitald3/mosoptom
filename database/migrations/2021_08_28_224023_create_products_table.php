<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->index();
            $table->unsignedBigInteger('manufacturer_id')->nullable()->index();
            $table->integer('layout_id');
            $table->unsignedBigInteger('stock_status_id')->index();
            $table->integer('popular')->nullable();
            $table->integer('quantity')->nullable()->index();
            $table->string('slug')->index();
            $table->string('image');
            $table->string('model');
            $table->unique(['slug']);
            $table->decimal('price', 15, 2);
            $table->integer('sort')->index();
            $table->decimal('weight', 15, 2);
            $table->integer('reward');
            $table->integer('status')->index();
            $table->foreign('stock_status_id')->references('id')->on('status')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
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
        Schema::dropIfExists('products');
    }
}
