<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->unsignedBigInteger('storage_item_id')->after('storage_id');
            $table->foreign('storage_item_id')->references('id')->on('storage_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->dropForeign(['storage_item_id']);
            $table->dropColumn('storage_item_id');
        });
    }
};
