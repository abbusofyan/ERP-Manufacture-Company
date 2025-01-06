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
        Schema::table('orders', function (Blueprint $table) {
            // Drop columns from the orders table
            $table->dropColumn(['supplier_name', 'supplier_address', 'supplier_phone']);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // If needed, you can add back the columns in the down method
            $table->string('supplier_name');
            $table->text('supplier_address');
            $table->string('supplier_phone');
        });
    }
};
