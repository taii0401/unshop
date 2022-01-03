<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueKeyToUnshopCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unshop_cart', function (Blueprint $table) {
            $table->unique(["user_id", "product_id"], 'user_product_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unshop_cart', function (Blueprint $table) {
            $table->dropUnique('user_product_unique');
        });
    }
}
