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
        Schema::table('service_vehicle_parts', function (Blueprint $table) {
            $table->unsignedBigInteger('storage_item_id')->nullable()->after('service_order_id');
            $table->foreign('storage_item_id')->references('id')->on('storage_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_vehicle_parts', function (Blueprint $table) {
            $table->dropColumn('storage_item_id');
        });
    }
};
