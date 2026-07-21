<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilterCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filter_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->primary(['id', 'category_id']);
            $table->unsignedBigInteger('filter_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->foreign('filter_id')->references('id')->on('filters')->onDelete('cascade');
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
        Schema::dropIfExists('filter_category');
    }
}
