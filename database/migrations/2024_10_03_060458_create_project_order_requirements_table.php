<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOrderRequirementsTable extends Migration
{
    public function up()
    {
        Schema::create('project_order_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_order_id');
            $table->unsignedBigInteger('storage_item_id')->nullable();
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('project_order_id')
                ->references('id')
                ->on('project_orders')
                ->onDelete('cascade');

            $table->foreign('storage_item_id')
                ->references('id')
                ->on('storage_items')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_order_requirements');
    }
}
