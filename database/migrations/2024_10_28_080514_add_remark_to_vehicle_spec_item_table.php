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
            $table->string('remark')->nullable()->after('discount');
        });
    }

    public function down()
    {
        Schema::table('vehicle_spec_items', function (Blueprint $table) {
            $table->dropColumn('remark');
        });
    }
};
