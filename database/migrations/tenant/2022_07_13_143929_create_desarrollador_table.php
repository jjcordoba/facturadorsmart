<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDesarrolladorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('desarrollador')) {
            Schema::create('desarrollador', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable()->default('FACTURAPERU');
            });
            DB::table('desarrollador')->insert([
                ['name' => 'FACTURAPERU'],
             ]);
            }

    }

    public function down()
    {
        Schema::dropIfExists('desarrollador');
    }
}
