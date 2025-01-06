<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectWorkingHoursTable extends Migration
{
    public function up()
    {
        Schema::create('project_working_hours', function (Blueprint $table) {
            $table->id();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('specific_date')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_lunch_time')->default(false);
            $table->boolean('is_dinner_time')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_working_hours');
    }
}
