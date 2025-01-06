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
            $table->decimal('discount', 10, 2)->nullable()->after('value');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('discount');
            $table->decimal('tax_amount', 10, 2)->nullable()->after('tax_rate');
            $table->decimal('total', 10, 2)->nullable()->after('tax_amount');
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
            $table->dropColumn('discount');
            $table->dropColumn('tax_rate');
            $table->dropColumn('tax_amount');
            $table->dropColumn('total');
        });
    }
};
