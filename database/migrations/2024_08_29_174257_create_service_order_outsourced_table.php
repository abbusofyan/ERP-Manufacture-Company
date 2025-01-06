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
        Schema::create('service_order_outsourced', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Name of the outsourced service/item
            $table->integer('quantity'); // Quantity of the outsourced item
            $table->decimal('price', 10, 2); // Price of the outsourced item
            $table->string('file_url')->nullable(); // URL for any associated file
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_order_outsourced');
    }
};
