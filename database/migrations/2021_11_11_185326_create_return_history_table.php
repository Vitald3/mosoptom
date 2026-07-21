<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('comment', 300);
            $table->integer('status')->index();
            $table->unsignedBigInteger('return_id')->index();
            $table->foreign('return_id')->references('id')->on('returns')->onDelete('cascade');
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
        Schema::dropIfExists('return_history');
    }
}
