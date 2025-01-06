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
            $table->decimal('finance_amount', 10, 2)->nullable()->after('total_amount');
            $table->decimal('deposit_required', 10, 2)->nullable()->after('finance_amount');
            $table->decimal('rounding', 10, 2)->nullable()->after('deposit_required');
            $table->decimal('freight_charges', 10, 2)->nullable()->after('rounding');
            $table->string('customer_reference_number')->nullable()->after('freight_charges');
            $table->string('attachment')->nullable()->after('customer_reference_number');
            $table->text('amend_customer_address')->nullable()->after('attachment');
            $table->text('footer')->nullable()->after('amend_customer_address');
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
            $table->dropColumn([
                'finance_amount',
                'deposit_required',
                'rounding',
                'freight_charges',
                'customer_reference_number',
                'attachment',
                'amend_customer_address',
                'footer'
            ]);
        });
    }
};
