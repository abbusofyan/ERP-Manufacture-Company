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
        Schema::create('vendor_pocs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->unsignedBigInteger('country_phone_code_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email');
            $table->string('title')->nullable();
            $table->tinyInteger('status')->nullable()->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('country_phone_code_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_pocs');
    }
};
