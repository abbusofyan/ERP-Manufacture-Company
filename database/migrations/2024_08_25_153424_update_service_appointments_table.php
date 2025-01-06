<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServiceAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_appointments', function (Blueprint $table) {
            if (Schema::hasColumn('service_appointments', 'term_of_payment')) {
                $table->dropColumn('term_of_payment');
            }
            $table->string('service_order_type')->after('date_of_appointment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_appointments', function (Blueprint $table) {
            $table->string('term_of_payment')->after('date_of_appointment')->nullable();
            if (Schema::hasColumn('service_appointments', 'service_order_type')) {
                $table->dropColumn('service_order_type');
            }
        });
    }
}
