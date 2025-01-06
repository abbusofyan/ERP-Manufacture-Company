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
        Schema::table('service_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('warranty_sale_order_eco_id')->nullable()->after('status');
            $table->unsignedBigInteger('warranty_contract_id')->nullable()->after('warranty_sale_order_eco_id');

            // Optionally, you can add foreign keys if these columns reference other tables
            $table->foreign('warranty_sale_order_eco_id')->references('id')->on('sales_orders')->onDelete('set null');
            $table->foreign('warranty_contract_id')->references('id')->on('service_contracts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['warranty_sale_order_eco_id']);
            $table->dropForeign(['warranty_contract_id']);
            $table->dropColumn('warranty_sale_order_eco_id');
            $table->dropColumn('warranty_contract_id');
        });
    }
};
