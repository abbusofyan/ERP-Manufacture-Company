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
        Schema::create('refrigeration_sales', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->tinyInteger('shipment')->default(1);

            $table->string('photo')->nullable();

            $table->decimal('sub_total')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('gst')->nullable();
            $table->decimal('total')->nullable();

            $table->longText('note')->nullable();
            $table->longText('footer')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('refrigeration_sales');
    }
};
