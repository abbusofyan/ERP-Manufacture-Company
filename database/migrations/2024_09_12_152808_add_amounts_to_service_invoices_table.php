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
            $table->decimal('tax_amount', 10, 2)->default(0)->after('status');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('subtotal_amount', 10, 2)->default(0)->after('discount_amount');
            $table->decimal('total_amount', 10, 2)->default(0)->after('subtotal_amount');
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
            $table->dropColumn('tax_amount');
            $table->dropColumn('discount_amount');
            $table->dropColumn('subtotal_amount');
            $table->dropColumn('total_amount');
        });
    }
};
