<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDataTypeInUnshopFileData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unshop_file_data', function (Blueprint $table) {
            $table->renameColumn('date_type','data_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unshop_file_data', function (Blueprint $table) {
            $table->renameColumn('data_type','date_type');
        });
    }
}
