<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_quotations', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
            $table->unsignedBigInteger('service_quotation_id')->nullable()->after('code');
            $table->unsignedBigInteger('service_order_id')->nullable()->after('service_quotation_id');
            $table->unsignedBigInteger('status')->default(1)->after('service_order_id');
            $table->unsignedBigInteger('customer_id')->after('service_order_id');
            $table->unsignedBigInteger('vehicle_id')->after('customer_id');
            $table->string('pic_first_name')->nullable()->after('vehicle_id');
            $table->string('pic_last_name')->nullable()->after('pic_first_name');
            $table->string('pic_title')->nullable()->after('pic_last_name');
            $table->string('pic_country_code')->nullable()->after('pic_title');
            $table->string('pic_phone_number')->nullable()->after('pic_country_code');
            $table->string('pic_email')->nullable()->after('pic_phone_number');
            $table->string('service_order_type')->nullable()->after('pic_email');
            $table->string('service_order_value')->nullable()->after('service_order_type');
            $table->text('remark')->nullable()->after('service_order_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_quotations', function (Blueprint $table) {
            //
        });
    }
};
