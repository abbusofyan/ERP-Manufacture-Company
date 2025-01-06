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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('uom_id');
            $table->boolean('auto_reorder');
            $table->string('lead_time')->nullable();
            $table->integer('reorder_level')->nullable();
            $table->text('remark')->nullable();
            $table->integer('quantity_to_reorder')->nullable();
            $table->string('file_url')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('uom_id')->references('id')->on('unit_of_measurements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
