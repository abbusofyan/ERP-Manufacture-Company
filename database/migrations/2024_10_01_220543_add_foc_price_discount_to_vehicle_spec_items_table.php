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
        Schema::table('vehicle_spec_items', function (Blueprint $table) {
            $table->string('component')->after('qty'); // replace 'existing_field' with the actual field name
            $table->boolean('foc')->nullable()->after('component'); // replace 'existing_field' with the actual field name
            $table->integer('price')->after('foc');
            $table->integer('discount')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_spec_items', function (Blueprint $table) {
            //
        });
    }
};
