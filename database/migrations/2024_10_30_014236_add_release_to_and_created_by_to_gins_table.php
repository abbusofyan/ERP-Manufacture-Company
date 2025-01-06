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
        Schema::table('gins', function (Blueprint $table) {
            $table->unsignedBigInteger('release_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->after('release_to');

            $table->foreign('release_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gins', function (Blueprint $table) {
            //
        });
    }
};
