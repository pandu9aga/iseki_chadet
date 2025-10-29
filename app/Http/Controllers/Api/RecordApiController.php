<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        $data = $validated;

        // === HANDLE FILE UPLOAD ===
        if ($request->hasFile('Photo_Ng_Path')) {
            $file = $request->file('Photo_Ng_Path');

            // Generate nama acak 40 karakter dengan ekstensi asli
            $randomName = Str::random(40) . '.' . $file->getClientOriginalExtension();

            // Simpan di disk 'uploads' dalam folder 'ng_photos'
            $path = $file->storeAs('ng_photos', $randomName, 'uploads');

            // Masukkan ke data untuk disimpan di DB
            $data['Photo_Ng_Path'] = $path;
        } else {
            unset($data['Photo_Ng_Path']);
        }

        $processName = 'chadet';

        // --- LOGIKA UPDATE RECORD DI DATABASE PODIUM LANGSUNG ---
        // --- PERUBAHAN: Format sequence_no ---
        // Format No_Tractor_Record ke 5 digit dengan leading zero
        // Misal: "6731" -> "06731", "1" -> "00001", "12345" -> "12345"
        $sequenceNoFormatted = str_pad($request->No_Produksi, 5, '0', STR_PAD_LEFT);
        $timestamp = $now->format('Y-m-d H:i:s');

        try {
            // 1. Cari plan di database PODIUM berdasarkan Sequence_No_Plan (dengan format yang disesuaikan)
            $plan = DB::connection('podium')->table('plans')->where('Sequence_No_Plan', $sequenceNoFormatted)->first();
            if (!$plan) {
                return back()->withErrors(['general' => "Plan dengan Sequence_No_Plan '{$sequenceNoFormatted}' tidak ditemukan di sistem PODIUM."]);
            }

            $modelName = $plan->Model_Name_Plan;

            // 2. Cari rule di database PODIUM berdasarkan Type_Rule = Model_Name_Plan
            $rule = DB::connection('podium')->table('rules')->where('Type_Rule', $modelName)->first();
            if (!$rule) {
                return back()->withErrors(['general' => "Rule untuk model '{$modelName}' tidak ditemukan di sistem PODIUM."]);
            }

            // 3. Decode Rule_Rule
            $ruleSequence = json_decode($rule->Rule_Rule, true);
            if (!is_array($ruleSequence)) {
                return back()->withErrors(['general' => "Format rule untuk model '{$modelName}' rusak."]);
            }

            // 4. Cek apakah process_name ada dalam rule
            $position = null;
            foreach ($ruleSequence as $key => $process) {
                if ($process === $processName) {
                    $position = (int)$key;
                    break;
                }
            }

            if ($position === null) {
                return back()->withErrors(['general' => "Proses '{$processName}' tidak termasuk dalam rule untuk model '{$modelName}'."]);
            }

            // 5. Decode Record_Plan
            $record = [];
            if ($plan->Record_Plan) {
                $decodedRecord = json_decode($plan->Record_Plan, true);
                if (is_array($decodedRecord)) {
                    $record = $decodedRecord;
                }
                // Jika tidak array atau null, biarkan $record tetap array kosong
            }

            // 6. Cek apakah proses sebelumnya sudah dilakukan
            $previousProcessesDone = true;
            $missingPrevious = [];
            for ($i = 1; $i < $position; $i++) {
                $prevProcess = $ruleSequence[$i] ?? null;
                if ($prevProcess && !isset($record[$prevProcess])) {
                    $previousProcessesDone = false;
                    $missingPrevious[] = $prevProcess;
                }
            }

            if (!$previousProcessesDone) {
                return back()->withErrors(['general' => "Proses sebelumnya belum selesai: " . implode(', ', $missingPrevious)]);
            }

            // 7. Update record: tambahkan proses dan timestamp
            $record[$processName] = $timestamp;

            // 8. Simpan kembali ke database PODIUM
            DB::connection('podium')->table('plans')
                ->where('Id_Plan', $plan->Id_Plan)
                ->update(['Record_Plan' => json_encode($record, JSON_UNESCAPED_UNICODE)]);

            // Logika berhasil dihilangkan, bisa ditambahkan jika perlu

        } catch (\Exception $e) {
            // Jika terjadi exception saat update database PODIUM
            return back()->withErrors(['general' => 'Gagal mencatat ke sistem PODIUM: ' . $e->getMessage()]);
        }

        // === UPDATE / INSERT RECORD ===
        $record = Record::updateOrCreate(
            ['No_Produksi' => $data['No_Produksi']],
            $data
        );

        // === RESPON JSON ===
        return response()->json([
            'success' => true,
            'message' => $record->wasRecentlyCreated
                ? 'Record created successfully'
                : 'Record updated successfully',
            'data' => $record
        ], $record->wasRecentlyCreated ? 201 : 200);
    }

    public function storeold(Request $request)
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
