<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index()
    {
        return view('ocr');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'photo' => 'required|image'
        ]);

        $path = $request->file('photo')->store('uploads');
        $filename = basename($path);

        return redirect('/')->with('image', asset('storage/uploads/' . $filename));
    }
}

