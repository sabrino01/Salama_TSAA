<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsersAddResponsablesIdSuivisId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table){
            $table->foreignId("responsables_id")->nullable()->constrained("responsables")->cascadeOnDelete();
            $table->foreignId("suivis_id")->nullable()->constrained("suivis")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("users", function (Blueprint $table){
            $table->dropColumn("responsables_id");
            $table->dropColumn("suivis_id");
        });
    }
}
