<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectOrderOutsourcedTable extends Migration
{
    public function up()
    {
        Schema::create('project_order_outsourced', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_order_id');
            $table->string('name');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->string('file_url')->nullable();
            $table->timestamps();

            $table->foreign('project_order_id', 'po_outsourced_order_id_fk')
                ->references('id')
                ->on('project_orders')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_order_outsourced');
    }
}
