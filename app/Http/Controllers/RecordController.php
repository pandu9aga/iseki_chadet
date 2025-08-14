<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record; // Jangan lupa import model User


class RecordController extends Controller
{
    public function index()
    {
        $page = 'record';
        $records = Record::all(); // Ambil semua data user
        return view('members.record', compact('page', 'records'));
    }
}

