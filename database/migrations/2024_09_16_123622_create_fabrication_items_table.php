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
        Schema::create('fabrication_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fabrication_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('assembly_id')->nullable();
            $table->string('qty')->nullabl();
            $table->string('type')->nullabl(); //input / output
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fabrication_items');
    }
};
