<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang digunakan (opsional, jika nama tabel tidak sesuai dengan konvensi Laravel)
    protected $table = 'dataset';

    // Tentukan kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'nama_platform_e_wallet',
        'VTP',
        'NTP',
        'PPE',
        'FPE',
        'PSD',
        'IPE',
        'PKP'
    ];

    // Tentukan kolom yang tidak boleh diisi melalui mass-assignment (jika ada)
    // protected $guarded = ['id']; // Biasanya, 'id' tidak perlu dimasukkan dalam $fillable atau $guarded.
}