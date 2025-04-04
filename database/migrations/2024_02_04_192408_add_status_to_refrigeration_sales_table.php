<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('refrigeration_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('status')->default(1)->after('post_type');
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
            $table->dropColumn('status');
        });
    }
};
