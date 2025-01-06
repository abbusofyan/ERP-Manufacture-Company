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
        Schema::table('vehicle_spec_items', function (Blueprint $table) {
            $table->unsignedBigInteger('header_id')->nullable()->after('vehicle_spec_id');
            $table->foreign('header_id')->references('id')->on('vehicle_spec_headers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_spec_items', function (Blueprint $table) {
            $table->dropForeign(['header_id']);
            $table->dropColumn('header_id');
        });
    }
};
