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
            $table->string('type')->after('id')->nullable();
            $table->string('description')->nullable()->after('name');
            $table->integer('weight')->nullable()->after('uom_id');
            $table->integer('dimension_l')->nullable()->after('weight');
            $table->integer('dimension_w')->nullable()->after('dimension_l');
            $table->integer('dimension_h')->nullable()->after('dimension_w');
            $table->boolean('assembly')->nullable()->after('dimension_h');
            $table->integer('case_pack')->nullable()->after('assembly');
            $table->string('image_preview_url')->nullable()->after('status');
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
            $table->dropColumn([
                'type',
                'description',
                'weight',
                'dimension_l',
                'dimension_w',
                'dimension_h',
                'assembly',
                'case_pack',
                'image_preview_url'
            ]);
        });
    }
};
