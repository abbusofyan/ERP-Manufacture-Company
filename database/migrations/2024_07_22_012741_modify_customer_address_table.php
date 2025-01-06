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
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropColumn(['address', 'building_name', 'unit_no']);

            $table->string('block')->nullable()->before('postal_code');
            $table->string('street_name')->nullable()->before('postal_code');
            $table->string('unit_level')->nullable()->before('postal_code');
            $table->string('unit_number')->nullable()->before('postal_code');
            $table->string('city')->nullable()->before('postal_code');
            $table->string('region')->nullable()->before('postal_code');
            $table->string('country')->nullable()->before('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->text('address')->nullable();
            $table->string('building_name')->nullable();
            $table->string('unit_no')->nullable();

            $table->dropColumn(['block', 'street_name', 'unit_level', 'unit_number', 'city', 'region', 'country']);
        });
    }
};
