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
            $table->dropForeign(['confirmed_user_id']);

            // Drop the confirmed_user_id column
            $table->dropColumn('confirmed_user_id');

            // Add new columns
            $table->string('confirmed_by')->nullable()->after('customer_id');
            $table->string('name')->nullable()->after('confirmed_by');
            $table->string('email')->nullable()->after('name');
            $table->string('phone_number')->nullable()->after('email');
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
            // Re-add the confirmed_user_id column
            $table->unsignedBigInteger('confirmed_user_id')->nullable()->after('customer_id');

            // Re-establish foreign key constraint
            $table->foreign('confirmed_user_id')->references('id')->on('users')->onDelete('set null');

            // Drop the newly added columns
            $table->dropColumn(['confirmed_by', 'name', 'email', 'phone_number']);
        });
    }
};
