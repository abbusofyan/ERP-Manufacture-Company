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
        Schema::create('refrigeration_sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code', 25)->unique('rs_invoice_code_unique')->nullable();
            $table->unsignedBigInteger('refrigeration_sale_id');
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('file_url')->nullable();
            $table->string('delivery_order_invoice_url')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('finance_amount', 15, 2)->default(0);
            $table->decimal('deposit_required', 15, 2)->default(0);
            $table->decimal('rounding', 15, 2)->default(0);
            $table->decimal('freight_charges', 15, 2)->default(0);
            $table->string('customer_reference_number')->nullable();
            $table->string('attachment')->nullable();
            $table->text('amend_customer_address')->nullable();
            $table->text('footer')->nullable();
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('refrigeration_sale_id', 'rs_rs_id_fk')
                ->references('id')
                ->on('refrigeration_sales')
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
        Schema::dropIfExists('refrigeration_sale_invoices');
    }
};
