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
        Schema::create('service_contract_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_contract_id')->constrained('service_contracts')->onDelete('cascade');
            $table->foreignId('storage_item_id')->constrained('storage_items')->onDelete('cascade');
            $table->string('name');
            $table->integer('quantity');
            $table->string('tax_code')->nullable();
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('subtotal_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
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
        Schema::dropIfExists('service_contract_pricing');
    }
};
