<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DetectController extends Controller
{
    public function index()
    {
        $page = 'detect';
        return view('members.detect', compact('page'));
    }

    public function result()
    {
        $page = 'detect';
        return view('members.result', compact('page'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image'
        ]);

        $path = $request->file('photo')->store('uploads');
        $filename = basename($path);

        return redirect('result')->with('image', asset('storage/uploads/' . $filename));
    }
}

