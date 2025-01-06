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
            $table->dropForeign(['requisition_id']);  // This drops the foreign key for 'requisition_id'
            $table->dropForeign(['requisition_item_id']);
            
            $table->dropColumn('requisition_id');
            $table->dropColumn('requisition_item_id');
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
            $table->unsignedBigInteger('requisition_id')->nullable();
            $table->unsignedBigInteger('requisition_item_id')->nullable();
        });
    }
};
