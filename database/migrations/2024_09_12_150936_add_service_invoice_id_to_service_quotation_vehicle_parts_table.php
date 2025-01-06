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
        Schema::table('service_quotation_vehicle_parts', function (Blueprint $table) {
            $table->unsignedBigInteger('service_invoice_id')->nullable()->after('id'); // Add 'service_invoice_id' column after 'id'

            // If you want to create a foreign key from 'service_invoice_id' to 'service_invoices', add the following line:
             $table->foreign('service_invoice_id')->references('id')->on('service_invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_quotation_vehicle_parts', function (Blueprint $table) {
            // If there is a foreign key, you need to drop it first
             $table->dropForeign(['service_invoice_id']);
            $table->dropColumn('service_invoice_id');
        });
    }
};
