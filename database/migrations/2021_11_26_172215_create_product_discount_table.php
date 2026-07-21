<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_discount', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('product_id')->index();
			$table->unsignedBigInteger('customer_group_id')->index();
			$table->integer('quantity')->index();
			$table->decimal('price', 15, 2);
			$table->date('date_start')->default('0000-00-00');
			$table->date('date_end')->default('0000-00-00');
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
			$table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
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
        Schema::dropIfExists('product_discount');
    }
}
