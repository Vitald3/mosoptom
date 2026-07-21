<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_description', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 10)->index();
            $table->string('name', 300);
            $table->index('name');
            $table->text('meta_title');
            $table->text('meta_description');
            $table->text('meta_keywords');
            $table->text('description');
            $table->unsignedBigInteger('category_id')->index();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
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
        Schema::dropIfExists('category_description');
    }
}
