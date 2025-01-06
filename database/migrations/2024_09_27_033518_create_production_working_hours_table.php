<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionWorkingHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_working_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('specific_date')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_lunch_time')->default(false);
            $table->boolean('is_dinner_time')->default(false);
            $table->timestamps();

            // Indexes for optimization
            $table->index('specific_date');
            $table->index('is_default');
            $table->index('is_lunch_time');
            $table->index('is_dinner_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_working_hours');
    }
}
