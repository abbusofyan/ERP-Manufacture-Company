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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origin_store_id');
            $table->unsignedBigInteger('destination_store_id');
            $table->unsignedBigInteger('author');
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('origin_store_id')->references('id')->on('stores');
            $table->foreign('destination_store_id')->references('id')->on('stores');
            $table->foreign('author')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
};
