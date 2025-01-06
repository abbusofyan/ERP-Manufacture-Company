<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('info_type');
            $table->string('code')->unique();
            $table->string('country_id')->nullable();
            $table->string('name')->nullable();
            $table->string('country_phone_code_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('uen')->nullable();
            $table->string('account_manager')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('service')->nullable();
            $table->text('remark')->nullable();
            $table->string('refrigeration_sales')->nullable();
            $table->string('project')->nullable();

            $table->string('address_location')->nullable();
            $table->string('address_unit_no')->nullable();
            $table->string('address_building_name')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->string('address_office_number')->nullable();

            $table->string('poc_first_name')->nullable();
            $table->string('poc_last_name')->nullable();
            $table->string('poc_email')->nullable();
            $table->string('poc_country_phone_code_id')->nullable();
            $table->string('poc_phone')->nullable();
            $table->string('poc_title')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('customers');
    }
}
