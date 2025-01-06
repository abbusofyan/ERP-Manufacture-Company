<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('project_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->nullable();
            $table->unsignedBigInteger('project_quotation_id')->nullable();
            $table->integer('type')->default(1);
            $table->string('name')->nullable();
            $table->string('file_url')->nullable();
            $table->string('delivery_order_invoice_url')->nullable();
            $table->string('status')->default('Not Invoiced');
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('finance_amount', 15, 2)->default(0);
            $table->boolean('deposit_required')->default(false);
            $table->decimal('rounding', 15, 2)->default(0);
            $table->decimal('freight_charges', 15, 2)->default(0);
            $table->string('customer_reference_number')->nullable();
            $table->text('attachment')->nullable();
            $table->text('amend_customer_address')->nullable();
            $table->text('footer')->nullable();
            $table->timestamps();

            $table->foreign('project_quotation_id')
                ->references('id')
                ->on('project_quotations')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_invoices');
    }
}
