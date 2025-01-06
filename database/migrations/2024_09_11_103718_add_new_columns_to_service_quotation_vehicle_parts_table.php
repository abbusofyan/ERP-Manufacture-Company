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
        Schema::table('service_quotation_vehicle_parts', function (Blueprint $table) {
            $table->string('tax_code')->nullable()->after('quantity');
            $table->string('tax_amount')->nullable()->after('tax_code');
            $table->decimal('discount', 10, 2)->nullable()->after('tax_amount');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('discount');
            $table->decimal('subtotal_amount', 10, 2)->nullable()->after('discount_amount');
            $table->decimal('total_amount', 10, 2)->nullable()->after('subtotal_amount');

            $table->boolean('is_foc')->default(false)->after('total_amount');
            $table->text('description')->nullable()->after('is_foc');
            $table->text('misc_description')->nullable()->after('description');
            $table->boolean('is_hide')->default(false)->after('misc_description');
        });
    }

    public function down()
    {
        Schema::table('service_quotation_vehicle_parts', function (Blueprint $table) {
            $table->dropColumn('tax_code');
            $table->dropColumn('discount');
            $table->dropColumn('discount_amount');
            $table->dropColumn('subtotal_amount');
            $table->dropColumn('total_amount');
        });
    }
};
