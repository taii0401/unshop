<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnshopProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unshop_product', function (Blueprint $table) {
            $table->id();
            $table->string('uuid',50);
            $table->integer('user_id');
            $table->integer('types');
            $table->string('serial_code',2)->nullable();
            $table->integer('serial_num')->nullable();
            $table->string('serial')->nullable();
            $table->string('name')->nullable();
            $table->string('author')->nullable();
            $table->string('office')->nullable();
            $table->date('publish')->nullable();
            $table->integer('price')->default(0);
            $table->integer('sales')->default(0);
            $table->longText('content')->nullable();
            $table->longText('category')->nullable();
            $table->integer('click')->default(0);
            $table->tinyInteger('is_delete')->default(0);
            $table->tinyInteger('is_display')->default(0);
            $table->dateTime('create_time')->nullable();
            $table->dateTime('modify_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unshop_product');
    }
}
