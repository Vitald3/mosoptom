<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 20)->index();
            $table->string('name', 128)->index();
            $table->decimal('discount', 15, 2);
            $table->decimal('total', 15, 2);
            $table->string('type', 1);
            $table->integer('logged');
            $table->datetime('date_start');
            $table->datetime('date_end');
            $table->integer('uses_total');
            $table->string('uses_customer', 11);
            $table->integer('status')->index();
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
        Schema::dropIfExists('coupon');
    }
}
