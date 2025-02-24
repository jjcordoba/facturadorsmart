<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowInternalCodePerson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('show_internal_code_person')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('show_internal_code_person');
        });
    }
}
