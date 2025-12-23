<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Record extends Model
{
    use HasFactory;

    protected $table = 'records';
    protected $primaryKey = 'Id_Record';
    public $timestamps = false;

    protected $fillable = [
        'No_Produksi',
        'Tgl_Produksi',
        'No_Chasis_Kanban',
        'No_Chasis_Scan',
        'Time',
        'Status_Record',
        'Photo_Ng_Path',
        'Id_User'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    // HAPUS atau KOMENTARI fungsi plan() yang lama seperti ini:
    /*
    public function plan()
    {
        // ... kode lama ...
        // return null; // atau kode yang tidak valid
    }
    */

    // Biarkan accessor ini aktif
    public function getPlanAttribute()
    {
        // Ambil No_Produksi dari record ini
        $noProduksi = $this->No_Produksi;

        // Konversi No_Produksi ke format 5 digit
        $noProduksi5Digit = str_pad($noProduksi, 5, '0', STR_PAD_LEFT);

        // Cari Plan yang sesuai
        // Gunakan koneksi 'podium' yang telah ditentukan di model Plan
        $plan = Plan::whereRaw('LPAD(?, 5, "0") = Sequence_No_Plan', [$noProduksi])
                    ->first(); // Menggunakan $noProduksi, bukan $noProduksi5Digit, karena LPAD akan menanganinya

        return $plan; // Akan mengembalikan objek Plan atau null
    }
}