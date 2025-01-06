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
            // Drop the foreign key if it exists
            $table->dropForeign(['confirmed_user_id']);
            $table->dropColumn('confirmed_user_id');
            $table->string('confirmed_by')->nullable()->after('customer_id');
        });
    }

    public function down()
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('confirmed_user_id')->nullable()->after('customer_id');
            $table->foreign('confirmed_user_id')->references('id')->on('customers')->onDelete('set null');
            $table->dropColumn('confirmed_by');
        });
    }
};
