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
        Schema::table('assemblies', function (Blueprint $table) {
            $table->dropColumn(['name', 'category', 'uom', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assemblies', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->string('uom')->nullable();
            $table->string('status')->nullable();
        });
    }
};
