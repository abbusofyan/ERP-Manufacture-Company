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
        Schema::table('refrigeration_sale_header_type_items', function (Blueprint $table) {
            $table->string('uom')->nullable()->after('discount');
            $table->decimal('tax_value', 8, 2)->nullable()->after('uom');
            $table->decimal('tax_amount', 8, 2)->nullable()->after('tax_value');
            $table->decimal('sub_total', 8, 2)->nullable()->after('tax_amount');
            $table->decimal('total', 8, 2)->nullable()->after('sub_total');
            $table->text('description')->nullable()->after('total');
            $table->text('misc_description')->nullable()->after('description');
            $table->boolean('hide')->nullable()->default(false)->after('misc_description');
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
            $table->dropColumn(['uom', 'tax_value', 'tax_amount', 'sub_total', 'total', 'description', 'misc_description', 'hide']);
        });
    }
};
