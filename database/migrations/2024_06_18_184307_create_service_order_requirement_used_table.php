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
        Schema::create('service_order_requirement_used', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_requirement_id')
                ->constrained()
                ->onDelete('cascade')
                ->index('soru_service_order_requirement_id');
            $table->foreignId('service_order_id')
                ->constrained()
                ->onDelete('cascade')
                ->index('soru_service_order_id');
            $table->integer('quantity');
            $table->integer('returned_quantity');
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
        Schema::dropIfExists('service_order_requirement_used');
    }
};
