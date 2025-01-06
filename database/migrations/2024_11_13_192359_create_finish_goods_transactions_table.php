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
        Schema::create('finish_goods_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('finish_goods_id');
            $table->integer('created_by');
            $table->integer('convert_qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finish_goods_transactions');
    }
};
