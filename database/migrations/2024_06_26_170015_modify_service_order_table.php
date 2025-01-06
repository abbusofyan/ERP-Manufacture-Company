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
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['service_appointment_id']);
            $table->dropColumn('service_appointment_id');
            $table->string('code')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('service_appointment_id');
            $table->foreign('service_appointment_id')->references('id')->on('service_appointments');
            $table->dropColumn('code');
        });
    }
};
