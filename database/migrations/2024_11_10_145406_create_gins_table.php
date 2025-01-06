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
        Schema::dropIfExists('gins');
        Schema::create('gins', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('transaction_code')->nullable();
            $table->text('remark')->nullable();
            $table->integer('status');
            $table->integer('issued_by')->nullable();
            $table->integer('issued_to')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('gins');
    }
};
