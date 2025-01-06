<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOrderRequirementUsedTable extends Migration
{
    public function up()
    {
        Schema::create('project_order_requirement_used', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_order_id');
            $table->unsignedBigInteger('project_order_requirement_id');
            $table->unsignedBigInteger('requisition_id')->nullable();
            $table->unsignedBigInteger('requisition_item_id')->nullable();
            $table->unsignedBigInteger('project_quotation_id')->nullable();
            $table->unsignedBigInteger('project_quotation_vehicle_part_id')->nullable();
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('project_order_id', 'por_order_id_fk')
                ->references('id')
                ->on('project_orders')
                ->onDelete('cascade');

            $table->foreign('project_order_requirement_id', 'por_requirement_id_fk')
                ->references('id')
                ->on('project_order_requirements')
                ->onDelete('cascade');

            $table->foreign('requisition_id', 'req_id_fk')
                ->references('id')
                ->on('requisitions')
                ->onDelete('set null');

            $table->foreign('requisition_item_id', 'req_item_id_fk')
                ->references('id')
                ->on('requisition_items')
                ->onDelete('set null');

            $table->foreign('project_quotation_id', 'por_quotation_id_fk')
                ->references('id')
                ->on('project_quotations')
                ->onDelete('set null');

            $table->foreign('project_quotation_vehicle_part_id', 'por_vehicle_part_id_fk')
                ->references('id')
                ->on('project_quotation_vehicle_parts')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_order_requirement_used');
    }
}
