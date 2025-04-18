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
        Schema::table('refrigeration_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('confirmed_user_id')->nullable()->after('status');
            $table->foreign('confirmed_user_id')
                ->references('id')
                ->on('customers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refrigeration_sales', function (Blueprint $table) {
            $table->dropForeign(['confirmed_user_id']);
            $table->dropColumn('confirmed_user_id');
        });
    }
};
