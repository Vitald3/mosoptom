<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLegalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_legal', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('customer_id')->index();
			$table->string('inn', 15)->nullable();
			$table->string('kpp', 54)->nullable();
			$table->integer('kontragent')->nullable();
			$table->integer('forma_sobstvennosti')->nullable();
			$table->string('ogrn', 54)->nullable();
			$table->string('address', 300)->nullable();
			$table->string('address2', 300)->nullable();
			$table->string('firstname', 54)->index();
			$table->string('lastname', 54);
			$table->string('email', 54)->index();
			$table->string('phone', 54)->index();
			$table->string('company', 100);
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
        Schema::dropIfExists('customer_legal');
    }
}
