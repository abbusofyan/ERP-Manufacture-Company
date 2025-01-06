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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('current_mileage')->nullable()->after('other_info');
            $table->date('mileage_date')->nullable()->after('current_mileage');
            $table->integer('warranty_mileage')->nullable()->after('mileage_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['current_mileage', 'mileage_date', 'warranty_mileage']);
        });
    }
};
