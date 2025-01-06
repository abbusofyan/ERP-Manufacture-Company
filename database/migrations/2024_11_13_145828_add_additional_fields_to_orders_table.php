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
        Schema::table('orders', function (Blueprint $table) {
            $table->date('purchase_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('terms')->nullable();
            $table->string('currency')->nullable();
            $table->string('criterion')->nullable();
            $table->decimal('sub_total', 15, 2)->nullable();
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('freight', 15, 2)->nullable();
            $table->decimal('gst', 15, 2)->nullable();
            $table->decimal('rounding', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();

            $table->string('delivery_address')->nullable()->change();
            $table->date('edd')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_date',
                'delivery_date',
                'payment_method',
                'terms',
                'currency',
                'criterion',
                'sub_total',
                'discount',
                'freight',
                'gst',
                'rounding',
                'total',
            ]);
        });
    }
};
