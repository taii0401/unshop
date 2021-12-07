<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnshopFileDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unshop_file_data', function (Blueprint $table) {
            $table->id();
            $table->integer('data_id');
            $table->string('date_type',100)->nullable();
            $table->integer('file_id');
            $table->integer('create_by')->nullable();
            $table->dateTime('create_time')->nullable();
            $table->integer('modify_by')->nullable();
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
        Schema::dropIfExists('unshop_file_data');
    }
}
