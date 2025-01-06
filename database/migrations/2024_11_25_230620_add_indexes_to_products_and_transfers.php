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
        Schema::table('products', function (Blueprint $table) {
            $table->index('name', 'idx_product_name'); // Add index to 'name' column
            $table->index('sku', 'idx_product_sku');   // Add index to 'sku' column
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->index('code', 'idx_transfer_code'); // Add index to 'code' column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_product_name'); // Drop index for 'name'
            $table->dropIndex('idx_product_sku');   // Drop index for 'sku'
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropIndex('idx_transfer_code'); // Drop index for 'code'
        });
    }
};
