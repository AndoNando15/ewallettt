<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatasetTable extends Migration
{
    public function up()
    {
        Schema::create('dataset', function (Blueprint $table) {
            $table->id();  // Kolom primary key dengan tipe id
            $table->string('nama_platform_e_wallet'); // Kolom Nama Platform E-Wallet
            $table->string('VTP');  // Kolom VTP
            $table->string('NTP');  // Kolom NTP
            $table->string('PPE');  // Kolom PPE
            $table->string('FPE');  // Kolom FPE
            $table->string('PSD');  // Kolom PSD
            $table->string('IPE');  // Kolom IPE
            $table->string('PKP');  // Kolom PKP
            $table->timestamps();  // Kolom created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('dataset');
    }
}