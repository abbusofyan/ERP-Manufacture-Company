<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentProjectOrderProcessIdToProjectOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('project_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('current_project_order_process_id')->nullable()->after('vehicle_id');

            $table->foreign('current_project_order_process_id')
                ->references('id')
                ->on('project_order_processes')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('project_orders', function (Blueprint $table) {
            $table->dropForeign(['current_project_order_process_id']);
            $table->dropColumn('current_project_order_process_id');
        });
    }
}
