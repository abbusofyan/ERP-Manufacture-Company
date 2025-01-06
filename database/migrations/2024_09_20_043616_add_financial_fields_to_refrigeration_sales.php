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
        Schema::table('refrigeration_sales', function (Blueprint $table) {
            $table->decimal('deposit_value', 15, 2)->nullable()->after('confirmed_user_id');
            $table->string('deposit_option')->nullable()->after('deposit_value');
            $table->decimal('finance_amount', 15, 2)->nullable()->after('deposit_option');
            $table->string('payment_method')->nullable()->after('finance_amount');
            $table->text('terms')->nullable()->after('payment_method');
            $table->date('validity_date')->nullable()->after('terms');
            $table->string('currency', 10)->nullable()->after('validity_date');
            $table->decimal('currency_rate', 15, 2)->nullable()->after('currency');
            $table->decimal('rounding', 15, 2)->nullable()->after('currency_rate');
            $table->string('mockup_image_url')->nullable()->after('rounding');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refrigeration_sales', function (Blueprint $table) {
            $table->dropColumn([
                'deposit_value',
                'deposit_option',
                'finance_amount',
                'payment_method',
                'terms',
                'validity_date',
                'currency',
                'currency_rate',
                'rounding'
            ]);
        });
    }
};
