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
        Schema::table('service_quotations', function (Blueprint $table) {
            $table->decimal('sub_total', 15, 2)->nullable()->after('remark');
            $table->decimal('discount', 15, 2)->nullable()->after('sub_total');
            $table->decimal('gst_rate', 5, 2)->nullable()->after('discount');
            $table->decimal('gst_total', 15, 2)->nullable()->after('gst_rate');
            $table->decimal('total', 15, 2)->nullable()->after('gst_total');
            $table->string('signed_image_url')->nullable()->after('total');
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
            $table->dropColumn([
                'sub_total',
                'discount',
                'gst_rate',
                'gst_total',
                'total',
                'signed_image_url',
            ]);
        });
    }
};
