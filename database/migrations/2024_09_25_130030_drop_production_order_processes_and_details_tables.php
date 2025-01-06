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
        Schema::dropIfExists('production_order_processes');
        Schema::dropIfExists('production_order_process_details');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('production_order_processes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('production_order_process_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
