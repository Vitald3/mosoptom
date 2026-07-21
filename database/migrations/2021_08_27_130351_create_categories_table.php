<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('layout_id');
            $table->string('slug')->index();
			$table->string('image');
			$table->string('image2')->nullable();
			$table->string('image3')->nullable();
            $table->unique(['parent_id','slug']);
            $table->integer('top')->nullable()->index();
            $table->integer('sort')->index();
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
        Schema::dropIfExists('categories');
    }
}
