<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_description', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 10)->index();
            $table->string('name', 300);
            $table->index('name');
            $table->text('meta_title');
            $table->text('meta_description');
            $table->text('meta_keywords');
			$table->text('description');
			$table->text('html');
            $table->unsignedBigInteger('page_id')->index();
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
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
        Schema::dropIfExists('page_description');
    }
}
