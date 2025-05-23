<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificationDescriptionColumnInActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    public function down()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('description')->change(); // ou ajuste selon la taille d'origine
        });
    }

}
