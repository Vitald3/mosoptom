<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerNewsletterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_newsletter', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('customer_id')->index();
			$table->integer('type');
			$table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
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
        Schema::dropIfExists('customer_newsletter');
    }
}
