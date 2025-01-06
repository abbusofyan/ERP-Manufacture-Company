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
        Schema::table('pocs', function (Blueprint $table) {
            $table->unsignedBigInteger('country_fax_code_id')->nullable()->after('title');
            $table->string('fax')->nullable()->after('country_fax_code_id');
            $table->text('remark')->nullable()->after('fax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pocs', function (Blueprint $table) {
            $table->dropColumn('country_fax_code_id');
            $table->dropColumn('fax');
            $table->dropColumn('remark');
        });
    }
};
