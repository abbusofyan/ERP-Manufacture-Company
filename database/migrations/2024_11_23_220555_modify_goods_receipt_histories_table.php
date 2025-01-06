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
        Schema::table('goods_receipt_histories', function (Blueprint $table) {
            $table->dropColumn('received_qty');
            $table->float('quantity')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_receipt_histories', function (Blueprint $table) {
            $table->integer('received_qty')->nullable()->after('id');
            $table->dropColumn('quantity'); 
        });
    }
};
