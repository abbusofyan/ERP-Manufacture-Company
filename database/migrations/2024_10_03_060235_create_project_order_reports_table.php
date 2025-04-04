<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOrderReportsTable extends Migration
{
    public function up()
    {
        Schema::create('project_order_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_order_id');
            $table->string('name');
            $table->string('file_url')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('project_order_id')
                ->references('id')
                ->on('project_orders')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_order_reports');
    }
}
