<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->index();
            $table->integer('layout_id');
            $table->string('slug')->index();
            $table->unique(['parent_id','slug']);
			$table->string('image');
			$table->text('css');
            $table->string('video');
            $table->integer('bottom')->nullable();
            $table->integer('top')->nullable()->index();
            $table->integer('sort')->nullable()->index();
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
        Schema::dropIfExists('pages');
    }
}
