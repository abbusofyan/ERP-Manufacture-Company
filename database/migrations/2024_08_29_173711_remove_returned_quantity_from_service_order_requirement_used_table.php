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
        Schema::table('service_order_requirement_used', function (Blueprint $table) {
            $table->dropColumn('returned_quantity');
        });
    }

    public function down()
    {
        Schema::table('service_order_requirement_used', function (Blueprint $table) {
            $table->integer('returned_quantity')->default(0); // Restore column if rolling back
        });
    }
};
