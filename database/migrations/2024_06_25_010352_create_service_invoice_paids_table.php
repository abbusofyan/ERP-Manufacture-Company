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
        Schema::create('service_invoice_paids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_invoice_id');
            $table->decimal('amount', 10, 2);
            $table->string('mode_of_payment')->nullable();
            $table->timestamps();

            $table->foreign('service_invoice_id')->references('id')->on('service_invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_invoice_paids');
    }
};
