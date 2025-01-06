<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('service_working_hours', function (Blueprint $table) {
            $table->id();
            $table->time('start_time')->default('08:30:00');
            $table->time('end_time')->default('20:30:00');
            $table->date('specific_date')->nullable();
            $table->boolean('is_default')->default(false)->nullable();
            $table->boolean('is_lunch_time')->default(false)->nullable();
            $table->boolean('is_dinner_time')->default(false)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_working_hours');
    }
};
