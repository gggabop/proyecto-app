<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_fk_customer')->references('id')->on('customers')->nullable();
            $table->foreignId('id_fk_loan')->references('id')->on('loans')->nullable();
            $table->text('note');
            $table->integer('type_note');
            $table->boolean('register_status_db_diary');
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
        Schema::dropIfExists('diaries');
    }
}
