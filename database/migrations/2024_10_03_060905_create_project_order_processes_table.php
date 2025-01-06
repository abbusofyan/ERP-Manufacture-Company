<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOrderProcessesTable extends Migration
{
    public function up()
    {
        Schema::create('project_order_processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_order_id');
            $table->string('stage');
            $table->timestamp('begin_datetime')->nullable();
            $table->string('name')->nullable();
            $table->decimal('standard_time', 8, 2)->nullable()->default(0);
            $table->decimal('total_time', 8, 2)->nullable()->default(0);
            $table->integer('manpower')->nullable();
            $table->decimal('overtime', 8, 2)->nullable()->default(0);
            $table->decimal('efficiency', 8, 2)->nullable()->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->foreign('project_order_id')
                ->references('id')
                ->on('project_orders')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_order_processes');
    }
}
