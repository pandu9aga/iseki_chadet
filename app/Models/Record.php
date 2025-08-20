<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $table = 'records';      // Nama tabel
    protected $primaryKey = 'Id_Record'; // Primary key

    public $timestamps = false;

    protected $fillable = [
        'No_Produksi',
        'No_Chasis_Kanban',
        'No_Chasis_Scan',
        'Time',
        'Status_Record',
        'Photo_Ng_Path'
    ];
}
