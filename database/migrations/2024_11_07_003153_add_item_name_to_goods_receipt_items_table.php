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
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('uom')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->dropColumn('uom');
        });
    }
};
