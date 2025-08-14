<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';      // Nama tabel
    protected $primaryKey = 'Id_User'; // Primary key

    public $timestamps = false;

    protected $fillable = [
        'Username_User',
        'Name_User',
        'Password_User',
    ];
}
