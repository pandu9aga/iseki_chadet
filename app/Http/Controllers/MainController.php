<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $page = 'home';
        return view('members.home', compact('page'));
    }
}

