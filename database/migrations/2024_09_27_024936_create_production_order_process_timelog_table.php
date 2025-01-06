<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionOrderProcessTimelogTable extends Migration
{
    public function up()
    {
        Schema::create('production_order_process_timelog', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('production_order_process_id');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('type');
            $table->timestamps();

            $table->foreign('production_order_process_id', 'pop_timelog_porder_proc_id_fk')
                ->references('id')
                ->on('production_order_processes')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('production_order_process_timelog', function (Blueprint $table) {
            $table->dropForeign('pop_timelog_porder_proc_id_fk');
        });

        Schema::dropIfExists('production_order_process_timelog');
    }
}
