<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::create('project_appointments', function (Blueprint $table) {
            $table->id();
            $table->string('cmp_number')->nullable();
            $table->text('customer_feedback')->nullable();
            $table->date('date_of_appointment')->nullable();
            $table->string('service_order_type')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('pic_first_name')->nullable();
            $table->string('pic_last_name')->nullable();
            $table->string('pic_title')->nullable();
            $table->string('pic_country_code')->nullable();
            $table->string('pic_phone_number')->nullable();
            $table->string('pic_email')->nullable();
            $table->text('remark')->nullable();
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('warranty_contract_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('warranty_contract_id')->references('id')->on('project_contracts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_appointments');
    }
}
