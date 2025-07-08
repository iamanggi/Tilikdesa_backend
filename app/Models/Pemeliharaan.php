<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_fasilitas',
        'foto',
        'deskripsi',
        'id_lokasi',
        'tgl_pemeliharaan',
        'laporan_id',
        'catatan',
    ];

    public function laporan()
    {
        return $this->belongsTo(Report::class);
    }

    public function lokasi()
{
    return $this->belongsTo(Lokasi::class, 'id_lokasi');
}

}
