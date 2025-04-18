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
        Schema::table('service_contracts', function (Blueprint $table) {
            $table->string('file_url')->nullable()->after('signed_image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_contracts', function (Blueprint $table) {
            $table->dropColumn('file_url');
        });
    }
};
