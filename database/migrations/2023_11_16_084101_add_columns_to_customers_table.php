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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('address_location_2')->after('address_location')->nullable();
            $table->string('credit_term')->after('address_location_2')->nullable();
            $table->string('credit_limit', 10, 2)->after('credit_term')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('address_location_2');
            $table->dropColumn('credit_term');
            $table->dropColumn('credit_limit');
        });
    }
};
