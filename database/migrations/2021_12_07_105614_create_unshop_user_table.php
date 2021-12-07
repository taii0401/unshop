<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnshopUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unshop_user', function (Blueprint $table) {
            $table->id();
            $table->string('uuid',50);
            $table->integer('user_id');
            $table->string('short_link',100)->nullable();
            $table->string('name',30)->nullable();
            $table->tinyInteger('sex')->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone',10)->nullable();
            $table->string('address')->nullable();
            $table->integer('file_id')->default(0);
            $table->tinyInteger('is_delete')->default(0);
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
        Schema::dropIfExists('unshop_user');
    }
}
