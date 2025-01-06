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
        Schema::table('service_invoices', function (Blueprint $table) {
            // Adding the new column after the 'file_url' column
            $table->string('delivery_order_invoice_url')->nullable()->after('file_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_invoices', function (Blueprint $table) {
            // Dropping the new column
            $table->dropColumn('delivery_order_invoice_url');
        });
    }
};
