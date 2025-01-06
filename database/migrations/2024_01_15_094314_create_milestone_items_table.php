<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('milestone_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('milestone_id');
            $table->foreign('milestone_id')->references('id')->on('milestones');
            $table->string('stage')->nullable();
            $table->boolean('active')->default(false);
            $table->date('calculated_due_date')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('milestones');
    }
};
