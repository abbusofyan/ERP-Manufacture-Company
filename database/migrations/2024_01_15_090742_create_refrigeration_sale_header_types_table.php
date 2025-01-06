<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('refrigeration_sale_header_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rs_header_id')->nullable();
            $table->foreign('rs_header_id')->references('id')->on('refrigeration_sale_headers');

            $table->string('box')->nullable();

            $table->text('description')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('refrigeration_sale_header_types');
    }
};
