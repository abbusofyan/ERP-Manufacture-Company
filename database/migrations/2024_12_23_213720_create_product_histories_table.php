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
        Schema::create('product_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->string('reference_model')->nullable();
            $table->unsignedInteger('reference_id')->nullable();
            $table->string('action')->nullable();
            $table->text('data')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('item_logs');
        Schema::dropIfExists('product_histories');
    }
};
