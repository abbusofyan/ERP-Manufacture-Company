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
            $table->unsignedBigInteger('service_maintain_id')->nullable()->after('id');
            $table->foreign('service_maintain_id')->references('id')->on('service_maintains')->onDelete('cascade');
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
            $table->dropForeign(['service_maintain_id']);
            $table->dropColumn('service_maintain_id');
        });
    }
};
