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
            $table->dropColumn('storage_id');

            $table->dropForeign(['storage_item_id']);
            $table->dropColumn('storage_item_id');
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
            $table->unsignedBigInteger('storage_id')->nullable();

            $table->foreign('storage_item_id')->references('id')->on('storage_items')->onDelete('cascade');
            $table->unsignedBigInteger('storage_item_id')->nullable();
        });
    }
};
