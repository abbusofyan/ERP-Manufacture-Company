<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOrderProcessTimelogsTable extends Migration
{
    public function up()
    {
        Schema::create('project_order_process_timelog', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_order_process_id');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();

            $table->foreign('project_order_process_id', 'po_process_timelog_fk')
                ->references('id')
                ->on('project_order_processes')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_order_process_timelog');
    }
}
