<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name_customer');
            $table->float('cedula_customer', 10, 0)->unique();
            $table->text('address_work_customer');
            $table->text('address_home_customer');
            $table->text('extra_address_customer');
            $table->float('cellphone_customer');
            $table->float('extra_cellphone_customer');
            $table->boolean('register_status_db_customer');
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
        Schema::dropIfExists('customers');
    }
}
