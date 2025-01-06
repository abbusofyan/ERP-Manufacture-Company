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
        Schema::create('service_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_quotation_id');
            $table->string('status');
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('service_quotation_id')
                ->references('id')
                ->on('service_quotations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_invoices');
    }
};
