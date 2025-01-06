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
        Schema::table('service_contract_pricing', function (Blueprint $table) {
            // Add new columns
            $table->string('type')->after('service_contract_id');

            $table->unsignedBigInteger('assembly_id')->nullable()->after('type');
            $table->foreign('assembly_id')->references('id')->on('assemblies')->onDelete('cascade');

            $table->unsignedBigInteger('product_id')->nullable()->after('assembly_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->decimal('price', 10, 2)->nullable()->after('assembly_id');

            // Drop the foreign key and the storage_item_id column
            $table->dropForeign(['storage_item_id']); // Drop foreign key constraint on storage_item_id
            $table->dropColumn('storage_item_id'); // Drop the storage_item_id column itself
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_contract_pricing', function (Blueprint $table) {
            // Re-add dropped columns and foreign keys
            $table->unsignedBigInteger('storage_item_id')->nullable()->after('product_id');
            $table->foreign('storage_item_id')->references('id')->on('storage_items')->onDelete('cascade');

            $table->dropColumn('type');
            $table->dropColumn('price');
            $table->dropForeign(['assembly_id']);
            $table->dropColumn('assembly_id');
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};
