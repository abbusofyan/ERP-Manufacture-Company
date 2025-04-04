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
        Schema::table('customer_banks', function (Blueprint $table) {
            $table->boolean('status')->default(false)->after('is_default');
        });
    }

    public function down()
    {
        Schema::table('customer_banks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
