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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('requisition_id')->nullable()->after('product_id');
            $table->unsignedBigInteger('requisition_item_id')->nullable()->after('requisition_id');

            $table->foreign('requisition_id')->references('id')->on('requisitions');
            $table->foreign('requisition_item_id')->references('id')->on('requisition_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('requisition_id');
            $table->dropColumn('requisition_item_id');

            $table->dropForeign(['requisition_id']);
            $table->dropForeign(['requisition_item_id']);
        });
    }
};
