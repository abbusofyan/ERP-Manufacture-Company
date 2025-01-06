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
        Schema::create('service_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('service_contract_number')->unique();
            $table->date('start_duration_date');
            $table->date('end_duration_date');
            $table->string('value')->nullable();
            $table->string('term_of_payment');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('pic_first_name');
            $table->string('pic_last_name');
            $table->string('pic_title')->nullable();
            $table->string('pic_country_code')->nullable();
            $table->string('pic_phone_number')->nullable();
            $table->string('pic_email')->nullable();
            $table->json('number_of_service')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_contracts');
    }
};
