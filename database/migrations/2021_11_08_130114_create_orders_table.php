<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('customer_group_id')->index();
            $table->unsignedBigInteger('order_status_id')->nullable()->index();
            $table->integer('currency_id')->index();
            $table->string('lang', 64)->index();
            $table->string('currency_code', 64);
            $table->float('currency_value');
            $table->string('firstname', 300)->index();
            $table->string('lastname', 300);
            $table->string('email', 300)->index();
            $table->string('phone', 300)->index();
            $table->text('fields')->nullable();
            $table->decimal('total', 15, 2);
            $table->string('shipping_method', 64);
            $table->string('payment_method', 64);
            $table->string('shipping_title', 64);
            $table->string('payment_title', 64);
			$table->string('comment', 300)->nullable();
			$table->integer('type');
			$table->string('inn', 20)->nullable();
			$table->string('company', 200)->nullable();
            $table->ipAddress('ip');
            $table->foreign('order_status_id')->references('id')->on('status')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
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
        Schema::dropIfExists('orders');
    }
}
