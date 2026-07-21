<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product', 300)->index();
            $table->string('model', 300)->index();
            $table->string('firstname', 300)->index();
            $table->string('lastname', 300);
            $table->string('email', 300)->index();
            $table->string('phone', 300)->index();
            $table->string('comment', 300);
            $table->integer('quantity')->index();
            $table->integer('status')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('customer_id')->index();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
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
        Schema::dropIfExists('returns');
    }
}
