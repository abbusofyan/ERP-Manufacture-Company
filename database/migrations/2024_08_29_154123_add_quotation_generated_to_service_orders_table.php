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
        Schema::table('service_orders', function (Blueprint $table) {
            $table->boolean('quotation_generated_after_update')->default(false);
        });
    }

    public function down()
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn('quotation_generated_after_update');
        });
    }
};
