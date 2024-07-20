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
        Schema::create('graduates', function (Blueprint $table) {
            $table->id();
            $table->json('main_competencies')->nullable();
            $table->json('support_competencies')->nullable();
            $table->json('graduate_competencies')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('graduates', function (Blueprint $table) {
            $table->dropColumn('main_competencies');
            $table->dropColumn('support_competencies');
            $table->dropColumn('graduate_competencies');
        });
    }
};
