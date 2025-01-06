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
        Schema::table('refrigeration_sale_headers', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'discount', 'unit_price']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refrigeration_sale_headers', function (Blueprint $table) {
            $table->integer('quantity')->nullable();
            $table->decimal('discount', 8, 2)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
        });
    }
};
