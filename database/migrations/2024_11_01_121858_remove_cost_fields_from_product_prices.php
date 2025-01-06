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
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropColumn(['last_cost', 'highest_cost', 'lowest_cost']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->decimal('last_cost', 8, 2)->nullable();
            $table->decimal('highest_cost', 8, 2)->nullable();
            $table->decimal('lowest_cost', 8, 2)->nullable();
        });
    }
};
