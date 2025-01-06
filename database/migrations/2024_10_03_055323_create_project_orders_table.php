<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('project_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_maintain_id')->nullable();
            $table->unsignedBigInteger('converted_project_appointment_id')->nullable();
            $table->string('code', 50)->nullable()->unique();
            $table->text('customer_feedback')->nullable();
            $table->string('stage')->nullable();
            $table->unsignedBigInteger('project_quotation_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('confirmed_user_id')->nullable();
//            $table->unsignedBigInteger('current_project_order_process_id')->nullable();
            $table->string('pic_first_name')->nullable();
            $table->string('pic_last_name')->nullable();
            $table->string('pic_title')->nullable();
            $table->string('pic_country_code')->nullable();
            $table->string('pic_phone_number')->nullable();
            $table->string('pic_email')->nullable();
            $table->string('project_order_type')->nullable();
            $table->date('plan_start_date')->nullable();
            $table->date('plan_complete_date')->nullable();
            $table->date('est_complete_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->boolean('in_warranty')->default(false);
            $table->boolean('come_back_job')->default(false);
            $table->text('remark')->nullable();
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('warranty_sale_order_eco_id')->nullable();
            $table->unsignedBigInteger('warranty_contract_id')->nullable();
            $table->boolean('change_of_specs')->default(false);
            $table->boolean('quotation_generated_after_update')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_maintain_id')->references('id')->on('project_maintains')->onDelete('set null');
            $table->foreign('project_quotation_id')->references('id')->on('project_quotations')->onDelete('set null');
            $table->foreign('technician_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('confirmed_user_id')->references('id')->on('customers')->onDelete('set null');
//            $table->foreign('current_project_order_process_id')->references('id')->on('project_order_processes')->onDelete('set null');
            $table->foreign('warranty_sale_order_eco_id')->references('id')->on('sales_orders_eco')->onDelete('set null');
            $table->foreign('warranty_contract_id')->references('id')->on('project_contracts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_orders');
    }
}
