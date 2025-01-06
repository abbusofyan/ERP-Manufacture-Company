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
        Schema::table('service_contracts', function (Blueprint $table) {
            $table->string('contract_period')->nullable()->after('end_duration_date');
            $table->date('contract_created_date')->nullable()->after('contract_period');
            $table->string('customer_reference_no')->nullable()->after('contract_created_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_contracts', function (Blueprint $table) {
            $table->dropColumn('contract_period');
            $table->dropColumn('contract_created_date');
            $table->dropColumn('customer_reference_no');
        });
    }
};
