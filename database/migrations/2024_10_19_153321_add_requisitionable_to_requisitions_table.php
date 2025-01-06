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
        Schema::dropIfExists('requisitionables');
        Schema::table('requisitions', function (Blueprint $table) {
            $table->unsignedBigInteger('requisitionable_id')->nullable();
            $table->string('requisitionable_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn(['requisitionable_id', 'requisitionable_type']);
        });
    }
};
