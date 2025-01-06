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
        Schema::create('pocs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->unsignedBigInteger('country_phone_code_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email');
            $table->string('title')->nullable();
            $table->boolean('is_default')->nullable()->default(false);
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
        Schema::dropIfExists('pocs');
    }
};
