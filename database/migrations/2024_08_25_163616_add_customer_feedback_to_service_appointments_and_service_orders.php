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
        Schema::table('service_appointments', function (Blueprint $table) {
            $table->text('customer_feedback')->nullable()->after('cmp_number');
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->text('customer_feedback')->nullable()->after('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_appointments', function (Blueprint $table) {
            $table->dropColumn('customer_feedback');
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn('customer_feedback');
        });
    }
};
