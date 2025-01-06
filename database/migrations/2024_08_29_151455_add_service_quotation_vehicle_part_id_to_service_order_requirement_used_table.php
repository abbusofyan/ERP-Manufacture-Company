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
        Schema::table('service_order_requirement_used', function (Blueprint $table) {
            $table->unsignedBigInteger('service_quotation_vehicle_part_id')->nullable()->after('service_quotation_id');

            // Add foreign key constraint if necessary
            $table->foreign('service_quotation_vehicle_part_id', 'sv_quotation_vh_part_id')
                ->references('id')
                ->on('service_quotation_vehicle_parts')
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
            $table->dropForeign(['service_quotation_vehicle_part_id']);
            $table->dropColumn('service_quotation_vehicle_part_id');
        });
    }
};
