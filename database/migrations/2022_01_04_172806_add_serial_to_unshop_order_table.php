<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSerialToUnshopOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unshop_order', function (Blueprint $table) {
            $table->string('serial_code',2)->nullable();
            $table->integer('serial_num')->nullable();
            $table->string('serial')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unshop_order', function (Blueprint $table) {
            $table->dropColumn('serial_code');
            $table->dropColumn('serial_num');
            $table->dropColumn('serial');
        });
    }
}
