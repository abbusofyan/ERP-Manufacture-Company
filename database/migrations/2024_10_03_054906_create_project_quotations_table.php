<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectQuotationsTable extends Migration
{
    public function up()
    {
        Schema::create('project_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->nullable();
//            $table->unsignedBigInteger('project_order_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('pic_first_name')->nullable();
            $table->string('pic_last_name')->nullable();
            $table->string('pic_title')->nullable();
            $table->string('pic_country_code')->nullable();
            $table->string('pic_phone_number')->nullable();
            $table->string('pic_email')->nullable();
            $table->string('project_order_type')->nullable();
            $table->decimal('project_order_value', 15, 2)->nullable()->default(0);
            $table->text('remark')->nullable();
            $table->text('footer_text')->nullable();
            $table->decimal('deposit_value', 15, 2)->nullable()->default(0);
            $table->string('deposit_option')->nullable();
            $table->decimal('finance_amount', 15, 2)->nullable()->default(0);
            $table->string('payment_method')->nullable();
            $table->text('terms')->nullable();
            $table->date('validity_date')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('currency_rate', 15, 6)->nullable()->default(1);
            $table->decimal('rounding', 15, 2)->nullable()->default(0);
            $table->decimal('sub_total', 15, 2)->nullable()->default(0);
            $table->decimal('discount', 15, 2)->nullable()->default(0);
            $table->decimal('gst_rate', 5, 2)->nullable()->default(0);
            $table->decimal('gst_total', 15, 2)->nullable()->default(0);
            $table->decimal('total', 15, 2)->nullable()->default(0);
            $table->string('signed_image_url')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

//            $table->foreign('project_order_id')->references('id')->on('project_orders')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_quotations');
    }
}
