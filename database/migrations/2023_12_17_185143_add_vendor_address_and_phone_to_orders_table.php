<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorAddressAndPhoneToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add new columns to the orders table
            $table->text('vendor_address')->nullable()->after('vendor_id');
            $table->string('vendor_phone')->nullable()->after('vendor_address');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Reverse the changes if needed
            $table->dropColumn(['vendor_address', 'vendor_phone']);
        });
    }
}
