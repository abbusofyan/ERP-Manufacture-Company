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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('assembly');
            $table->unsignedBigInteger('assembly_id')->nullable()->after('deleted_at');
            $table->unsignedBigInteger('fg_id')->nullable()->after('assembly_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('assembly')->nullable()->after('deleted_at'); 
            $table->dropColumn(['assembly_id', 'fg_id']);
        });
    }
};
