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
        Schema::table('stock_adjustment_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('adjustment_qty')->nullable();
            $table->decimal('before_qty', 10, 2)->after('price')->nullable();
            $table->decimal('after_qty', 10, 2)->after('before_qty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_adjustment_items', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('before_qty');
            $table->dropColumn('after_qty');
        });
    }
};
