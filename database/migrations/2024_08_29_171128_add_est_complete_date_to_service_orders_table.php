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
        Schema::table('service_orders', function (Blueprint $table) {
            // Adding the new column after `plan_complete_date`
            $table->date('est_complete_date')->nullable()->after('plan_complete_date');
        });
    }

    public function down()
    {
        Schema::table('service_orders', function (Blueprint $table) {
            // Dropping the column in case of rollback
            $table->dropColumn('est_complete_date');
        });
    }
};
