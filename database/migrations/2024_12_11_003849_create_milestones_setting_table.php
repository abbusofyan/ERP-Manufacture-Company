<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('milestones_setting', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the milestone');
            $table->integer('duration')->comment('Duration in days');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones_setting');
    }
};
