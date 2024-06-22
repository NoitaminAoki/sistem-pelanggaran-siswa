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
        Schema::create('student_sanctions', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_nip', 15)->nullable();
            $table->string('student_nis', 15)->nullable();
            $table->bigInteger('sanction_id')->unsigned()->nullable();
            $table->integer('poin_awal');
            $table->integer('poin_akhir');
            $table->string('catatan', 100)->nullable();
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
        Schema::dropIfExists('student_sanctions');
    }
};
