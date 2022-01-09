<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeInUnshopOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unshop_order', function (Blueprint $table) {
            $table->string('name',30)->nullable()->change();
            $table->string('phone',10)->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('payment',30)->nullable()->change();
            $table->string('send',30)->nullable()->change();
            $table->string('status',30)->nullable()->change();
            $table->longText('remark')->nullable();
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
            $table->dropColumn('remark');
        });
    }
}
