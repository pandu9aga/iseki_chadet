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

    public function getPlanAttribute()
    {
        $noProduksi = $this->No_Produksi;
        $tglProduksi = $this->Tgl_Produksi;

        $noProduksi5Digit = str_pad($noProduksi, 5, '0', STR_PAD_LEFT);

        $plan = Plan::whereRaw('LPAD(?, 5, "0") = Sequence_No_Plan', [$noProduksi5Digit])
                    ->where('Production_Date_Plan', $tglProduksi)
                    ->first();

        return $plan;
    }
}