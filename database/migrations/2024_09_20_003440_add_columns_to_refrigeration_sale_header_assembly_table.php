<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('refrigeration_sale_header_type_items', function (Blueprint $table) {
            $table->unsignedBigInteger('assembly_id')->nullable()->after('product_id');
            $table->foreign('assembly_id')
                ->references('id')
                ->on('assemblies')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('refrigeration_sale_header_type_items', function (Blueprint $table) {
            $table->dropForeign(['assembly_id']);
            $table->dropColumn('assembly_id');
        });
    }
};
