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
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('transfer_id'); // Replace 'some_column' with the column after which you want to add product_id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
};
