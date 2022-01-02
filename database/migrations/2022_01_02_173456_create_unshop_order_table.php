<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnshopOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unshop_order', function (Blueprint $table) {
            $table->id();
            $table->string('uuid',50);
            $table->integer('user_id');
            $table->string('name',30);
            $table->string('phone',10);
            $table->string('address');
            $table->integer('payment')->default(0);
            $table->integer('status')->default(0);
            $table->dateTime('create_time');
            $table->dateTime('modify_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unshop_order');
    }
}
