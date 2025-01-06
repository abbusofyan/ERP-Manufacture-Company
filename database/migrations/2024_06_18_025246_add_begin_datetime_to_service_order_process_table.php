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
        Schema::table('service_order_processes', function (Blueprint $table) {
            $table->dateTime('begin_datetime')->nullable()->after('id'); // replace 'existing_column' with the column name after which you want to add the new column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_order_processes', function (Blueprint $table) {
            $table->dropColumn('begin_datetime');
        });
    }
};
