<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnshopCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unshop_code', function (Blueprint $table) {
            $table->id();
            $table->string('types',100)->nullable();
            $table->string('code',30)->nullable();
            $table->string('name',100)->nullable();
            $table->string('cname',100)->nullable();
            $table->tinyInteger('is_delete')->default(0);
            $table->tinyInteger('is_display')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unshop_code');
    }
}
