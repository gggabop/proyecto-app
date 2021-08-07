<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_customer_id')->references('id')->on('customers');
            $table->double('amount_cash_order',20,2);
            $table->integer('status_cash_order');
            $table->boolean('register_status_db_cashOrder');
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
        Schema::dropIfExists('cash_orders');
    }
}
