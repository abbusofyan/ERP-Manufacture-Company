<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectInvoicePaidTable extends Migration
{
    public function up()
    {
        Schema::create('project_invoice_paids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_invoice_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('mode_of_payment')->nullable();
            $table->timestamps();

            $table->foreign('project_invoice_id')
                ->references('id')
                ->on('project_invoices')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_invoice_paids');
    }
}
