<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectQuotationVehiclePartsTable extends Migration
{
    public function up()
    {
        Schema::create('project_quotation_vehicle_parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_quotation_id');
            $table->unsignedBigInteger('project_invoice_id')->nullable();
            $table->unsignedBigInteger('storage_item_id')->nullable();

            $table->string('name');
            $table->integer('quantity');

            $table->string('tax_code')->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->boolean('is_foc')->default(false);
            $table->text('description')->nullable();
            $table->text('misc_description')->nullable();
            $table->boolean('is_hide')->default(false);
            $table->timestamps();

            $table->foreign('project_quotation_id')
                ->references('id')
                ->on('project_quotations')
                ->onDelete('cascade');

            $table->foreign('project_invoice_id')
                ->references('id')
                ->on('project_invoices')
                ->onDelete('set null');

            $table->foreign('storage_item_id')
                ->references('id')
                ->on('storage_items')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_quotation_vehicle_parts');
    }
}
