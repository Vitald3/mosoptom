<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_group_id')->index();
            $table->unsignedBigInteger('address_id')->index();
            $table->integer('newsletter');
            $table->string('firstname', 54)->index();
            $table->string('lastname', 54);
            $table->string('email', 54)->index();
            $table->string('phone', 54);
            $table->ipAddress('ip');
            $table->integer('status')->index();
            $table->integer('approval');
			$table->string('password', 300);
            $table->string('salt', 9);
			$table->string('token', 300)->nullable();
			$table->integer('type')->nullable()->index();
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('address')->onDelete('cascade');
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
        Schema::dropIfExists('customers');
    }
}
