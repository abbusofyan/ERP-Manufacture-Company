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
        Schema::table('service_invoices', function (Blueprint $table) {
            $table->string('type')->nullable()->after('status');
            $table->string('name')->nullable()->after('type');
            $table->string('file_url')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_invoices', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('name');
            $table->dropColumn('file_url');
        });
    }
};
