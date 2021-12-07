<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnshopFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unshop_file', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('file_name')->unique();
            $table->string('path')->nullable();
            $table->string('size',30)->nullable();
            $table->string('types',30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unshop_file');
    }
}
