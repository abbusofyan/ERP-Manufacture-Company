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
        Schema::table('refrigeration_sale_header_type_items', function (Blueprint $table) {
            $table->renameColumn('discount', 'discount_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refrigeration_sale_header_type_items', function (Blueprint $table) {
            $table->renameColumn('discount_value', 'discount');
        });
    }
};
