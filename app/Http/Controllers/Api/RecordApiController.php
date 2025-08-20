<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecordApiController extends Controller
{
    // public function store(Request $request)
    // {
    //     // Validasi input
    //     $validated = $request->validate([
    //         'No_Produksi'      => 'required|string|max:255',
    //         'No_Chasis_Kanban' => 'nullable|string|max:255',
    //         'No_Chasis_Scan'   => 'nullable|string|max:255',
    //         'Status_Record'    => 'required|string|max:50',
    //     ]);

    //     $validated['Time'] = now();
        
    //     // Insert or Update berdasarkan No_Produksi
    //     $record = Record::updateOrCreate(
    //         ['No_Produksi' => $validated['No_Produksi']], // kondisi pencarian
    //         $validated // data yang diupdate/insert
    //     );

    //     // Kembalikan respons JSON
    //     return response()->json([
    //         'success' => true,
    //         'message' => $record->wasRecentlyCreated
    //             ? 'Record created successfully'
    //             : 'Record updated successfully',
    //         'data' => $record
    //     ], $record->wasRecentlyCreated ? 201 : 200);
    // }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'No_Produksi'      => 'required|string|max:255',
            'No_Chasis_Kanban' => 'nullable|string|max:255',
            'No_Chasis_Scan'   => 'nullable|string|max:255',
            'Status_Record'    => 'required|string|max:50',
            'Photo_Ng_Path'    => 'nullable|file|image', // max 2MB
        ]);

        $validated['Time'] = now();

        // Kalau ada file foto
        if ($request->hasFile('Photo_Ng_Path')) {
            $path = $request->file('Photo_Ng_Path')->store('ng_photos', 'uploads');
            $validated['Photo_Ng_Path'] = $path;
        }

        // Insert or Update berdasarkan No_Produksi
        $record = Record::updateOrCreate(
            ['No_Produksi' => $validated['No_Produksi']], 
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => $record->wasRecentlyCreated
                ? 'Record created successfully'
                : 'Record updated successfully',
            'data' => $record
        ], $record->wasRecentlyCreated ? 201 : 200);
    }

    public function view(Request $request)
    {
        // Default hari ini, format MySQL: YYYY-MM-DD
        $date = $request->input('Day_Record', Carbon::today()->toDateString()); 
        // Carbon::toDateString() = '2025-08-14'

        try {
            // Pastikan format sesuai MySQL
            $parsedDate = Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Expected Y-m-d.'
            ], 400);
        }

        // Query ke DB dengan format MySQL
        $records = Record::whereDate('Time', $parsedDate->format('Y-m-d'))->get();

        return response()->json([
            'success' => true,
            'date'    => $parsedDate->format('Y-m-d'), // output ke Kotlin
            'count'   => $records->count(),
            'data'    => $records
        ]);
    }
}
