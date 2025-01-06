<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectContractInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('project_contract_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_contract_id');
            $table->integer('index')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status');
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('footer')->nullable();
            $table->string('file_url')->nullable();
            $table->timestamps();

            $table->foreign('project_contract_id')
                ->references('id')
                ->on('project_contracts')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contract_invoices');
    }
}
