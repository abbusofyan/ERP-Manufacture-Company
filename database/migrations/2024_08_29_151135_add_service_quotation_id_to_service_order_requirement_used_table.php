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
            $table->unsignedBigInteger('service_quotation_id')->nullable()->after('requisition_item_id');

            // Add foreign key constraint if necessary
            $table->foreign('service_quotation_id')
                ->references('id')
                ->on('service_quotations')
                ->onDelete('set null');
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
            $table->dropForeign(['service_quotation_id']);
            $table->dropColumn('service_quotation_id');
        });
    }
};
