<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('storage_items', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->change();
        });

        Schema::table('storage_items', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('storage_items', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id');
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });
    }
};
