<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectContractsTable extends Migration
{
    public function up()
    {
        Schema::create('project_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('project_contract_number', 50)->nullable()->unique('pro_contract_number_unique');
            $table->date('start_duration_date')->nullable();
            $table->date('end_duration_date')->nullable();
            $table->integer('contract_period')->nullable();
            $table->date('contract_created_date')->nullable();
            $table->string('customer_reference_no')->nullable();
            $table->decimal('value', 15, 2)->nullable()->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->nullable()->default(0);
            $table->decimal('tax_rate', 5, 2)->nullable()->default(0);
            $table->decimal('tax_amount', 15, 2)->nullable()->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('term_of_payment')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('pic_first_name')->nullable();
            $table->string('pic_last_name')->nullable();
            $table->string('pic_title')->nullable();
            $table->string('pic_country_code')->nullable();
            $table->string('pic_phone_number')->nullable();
            $table->string('pic_email')->nullable();
            $table->text('remark')->nullable();
            $table->integer('status')->default(0);
            $table->string('signed_image_url')->nullable();
            $table->string('file_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contracts');
    }
}
