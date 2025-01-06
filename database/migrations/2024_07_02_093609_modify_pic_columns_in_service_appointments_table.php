<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPicColumnsInServiceAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_appointments', function (Blueprint $table) {
            $table->string('pic_first_name')->nullable()->change();
            $table->string('pic_last_name')->nullable()->change();
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
            $table->string('pic_first_name')->nullable(false)->change();
            $table->string('pic_last_name')->nullable(false)->change();
        });
    }
}
