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
        Schema::table('store_products', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');

            $table->integer('reserved_qty')->default(0)->after('stock');
            $table->integer('requested_qty')->default(0)->after('reserved_qty');
            $table->integer('committed_qty')->default(0)->after('requested_qty');
            $table->integer('ordered_qty')->default(0)->after('committed_qty');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_products', function (Blueprint $table) {
            $table->dropColumn(['reserved_qty', 'requested_qty', 'committed_qty', 'ordered_qty']);

            $table->integer('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }
};
