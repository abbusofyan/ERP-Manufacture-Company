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
        Schema::create('project_contract_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_contract_id');
            $table->string('type')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('assembly_id')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('name')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('tax_code')->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->nullable()->default(0);
            $table->decimal('discount_amount', 15, 2)->nullable()->default(0);
            $table->decimal('subtotal_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('project_contract_id')
                ->references('id')
                ->on('project_contracts')
                ->onDelete('cascade');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('assembly_id')->references('id')->on('assemblies')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_contract_pricing');
    }
};
