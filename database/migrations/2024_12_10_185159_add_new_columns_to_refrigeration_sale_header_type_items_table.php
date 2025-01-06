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
        Schema::table('refrigeration_sale_header_type_items', function (Blueprint $table) {
            $table->string('discount_type', 50)->nullable()->after('sub_total');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('discount_type');
            $table->string('tax_code', 20)->nullable()->after('discount_amount');
            $table->integer('quantity_on_stock')->nullable()->after('hide');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refrigeration_sale_header_type_items', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_amount', 'tax_code', 'quantity_on_stock']);
        });
    }
};
