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
        Schema::table('project_quotations', function (Blueprint $table) {
            $table->unsignedBigInteger('project_order_id')->nullable()->after('id');

            $table->foreign('project_order_id')
                ->references('id')
                ->on('project_orders')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('project_quotations', function (Blueprint $table) {
            $table->dropForeign(['project_order_id']);
            $table->dropColumn('project_order_id');
        });
    }
};
