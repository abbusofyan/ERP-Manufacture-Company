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
        Schema::table('service_order_requirement_used', function (Blueprint $table) {
            // Add the requisition_item_id column
            $table->unsignedBigInteger('requisition_item_id')->nullable()->after('requisition_id');

            // Optionally, add a foreign key constraint
            $table->foreign('requisition_item_id')->references('id')->on('requisition_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_order_requirement_used', function (Blueprint $table) {
            // Remove the foreign key constraint if it exists
            $table->dropForeign(['requisition_item_id']);

            // Drop the requisition_item_id column
            $table->dropColumn('requisition_item_id');
        });
    }
};
