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
        Schema::table('requisitions', function (Blueprint $table) {
            $table->unsignedBigInteger('service_order_id')->nullable()->after('id');
            $table->unsignedBigInteger('requested_by')->nullable()->after('service_order_id');

            // Foreign key constraints (optional)
            $table->foreign('service_order_id')->references('id')->on('service_orders')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropForeign(['service_order_id']);
            $table->dropForeign(['requested_by']);
            $table->dropColumn(['service_order_id', 'requested_by']);
        });
    }
};
