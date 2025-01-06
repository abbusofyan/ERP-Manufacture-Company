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
        Schema::create('vehicle_spec_headers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('vehicle_spec_headers')->insert([
            ['name' => 'FREEZER BOX'],
            ['name' => 'FREEZER DOOR'],
            ['name' => 'REFRIGERATION UNIT'],
            ['name' => 'FLOORING & SIDEWALL'],
            ['name' => 'ELECTRICAL STANDBY'],
            ['name' => 'ACCESSORIES'],
            ['name' => 'OTHERS'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_spec_headers');
    }
};
