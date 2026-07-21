<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeGroupDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_group_description', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('lang', 10);
			$table->string('name', 300);
			$table->index('name');
			$table->index('lang');
			$table->unsignedBigInteger('attribute_group_id');
			$table->foreign('attribute_group_id')->references('id')->on('attribute_groups')->onDelete('cascade');
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
        Schema::dropIfExists('attribute_group_description');
    }
}
