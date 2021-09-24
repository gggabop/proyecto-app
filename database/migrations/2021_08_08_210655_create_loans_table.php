<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_id_cliente')->references('id')->on('customers');
            $table->foreignId('fk_id_cashOrder')->references('id')->on('cash_orders')->nullable(true);
            $table->integer('status_loan')->nullable();
            $table->double('amount_loan',20,2);
            $table->double('amount_rest_loan',20,2)->nullable();
            $table->double('debt_loan',20,2)->nullable();
            $table->date('date_start_loan');
            $table->date('date_pay_loan');
            $table->integer('interest_rate_loan');
            $table->integer('register_status_db_loan')->default(0);
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
        Schema::dropIfExists('loans');
    }
}
