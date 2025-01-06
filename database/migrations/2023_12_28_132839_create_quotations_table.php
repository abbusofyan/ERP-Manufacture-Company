<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('post_type')->default(1);

            $table->string('code')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->tinyInteger('shipment')->default(1);

            $table->decimal('sub_total')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('gst')->nullable();
            $table->decimal('total')->nullable();

            $table->longText('remarks')->nullable();

            $table->tinyInteger('status')->default(1);
            $table->string('photo')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
