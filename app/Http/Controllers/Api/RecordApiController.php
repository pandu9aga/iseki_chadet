<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

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

    public function storenew(Request $request)
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
                return response()->json([
                    'success' => false,
                    'message' => "Plan dengan Sequence_No_Plan '{$sequenceNoFormatted}' tidak ditemukan di sistem PODIUM."
                ], 404);
            }

            $modelName = $plan->Model_Name_Plan;

            // 2. Cari rule di database PODIUM berdasarkan Type_Rule = Model_Name_Plan
            $rule = DB::connection('podium')->table('rules')->where('Type_Rule', $modelName)->first();
            if (!$rule) {
                return response()->json([
                    'success' => false,
                    'message' => "Rule untuk model '{$modelName}' tidak ditemukan di sistem PODIUM."
                ], 400);
            }

            // 3. Decode Rule_Rule (ini berupa string JSON dari Query Builder)
            $ruleSequenceRaw = $rule->Rule_Rule;
            $ruleSequence = null;
            if (is_string($ruleSequenceRaw)) {
                $ruleSequence = json_decode($ruleSequenceRaw, true);
            }
            if (!is_array($ruleSequence)) {
                return response()->json([
                    'success' => false,
                    'message' => "Format rule untuk model '{$modelName}' rusak atau tidak valid."
                ], 400);
            }

            // 4. Cek apakah process_name ('chadet') ada dalam rule
            $position = null;
            foreach ($ruleSequence as $key => $process) {
                if ($process === $processName) {
                    $position = (int)$key;
                    break;
                }
            }

            if ($position === null) {
                return response()->json([
                    'success' => false,
                    'message' => "Proses '{$processName}' tidak termasuk dalam rule untuk model '{$modelName}'."
                ], 400);
            }

            // 5. Decode Record_Plan (ini berupa string JSON dari Query Builder)
            $recordPlanRaw = $plan->Record_Plan;
            $record = [];
            if (is_string($recordPlanRaw) && !empty($recordPlanRaw)) {
                $decodedRecord = json_decode($recordPlanRaw, true);
                if (is_array($decodedRecord)) {
                    $record = $decodedRecord;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Format Record_Plan untuk plan ini rusak."
                    ], 500); // atau 400
                }
            } // Jika null atau kosong, biarkan $record sebagai array kosong

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
                return response()->json([
                    'success' => false,
                    'message' => "Proses sebelumnya belum selesai: " . implode(', ', $missingPrevious)
                ], 400);
            }

            // 7. Update record: tambahkan proses 'chadet' dan timestamp
            $record[$processName] = $timestamp;

            // 8. Simpan kembali ke database PODIUM
            DB::connection('podium')->table('plans')
                ->where('Id_Plan', $plan->Id_Plan)
                ->update(['Record_Plan' => json_encode($record, JSON_UNESCAPED_UNICODE)]);

            // --- LOGIKA TAMBAHAN: Cek apakah SEMUA proses dalam rule sekarang sudah selesai ---
            $allProcessesCompleted = true;
            $processesMissing = []; // Untuk logging/debugging jika perlu

            foreach ($ruleSequence as $expectedProcessName) {
                if (!isset($record[$expectedProcessName])) {
                    $allProcessesCompleted = false;
                    $processesMissing[] = $expectedProcessName; // Tambahkan ke daftar yang belum selesai
                    // Kita tidak perlu break, kita ingin tahu semua yang belum selesai jika perlu log
                }
            }

            // Jika semua proses selesai, update Status_Plan di database PODIUM
            if ($allProcessesCompleted) {
                DB::connection('podium')->table('plans')
                    ->where('Id_Plan', $plan->Id_Plan) // Gunakan Id_Plan untuk keamanan
                    ->update(['Status_Plan' => 'done']);

                \Log::info("Status_Plan diupdate menjadi 'done' untuk Id_Plan: {$plan->Id_Plan} (Sequence: {$sequenceNoFormatted}) karena semua proses selesai setelah menambahkan '{$processName}'.");
            } else {
                // Opsional: Log proses yang masih belum selesai
                // \Log::debug("Status_Plan tidak diupdate untuk Id_Plan: {$plan->Id_Plan} (Sequence: {$sequenceNoFormatted}). Proses yang belum selesai: " . implode(', ', $processesMissing));
            }
            // --- AKHIR LOGIKA TAMBAHAN ---

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat ke sistem PODIUM: ' . $e->getMessage()
            ], 500);
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

    public function checkPrerequisites(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sequence_no' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $sequenceNo = $request->input('sequence_no');
            $sequenceNoFormatted = str_pad($sequenceNo, 5, '0', STR_PAD_LEFT);

            // Log::info("CheckPrerequisites called for sequence: $sequenceNoFormatted");

            // 1. Cari plan di database PODIUM
            $plan = DB::connection('podium')->table('plans')->where('Sequence_No_Plan', $sequenceNoFormatted)->first();
            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => "Plan dengan Sequence_No_Plan '{$sequenceNoFormatted}' tidak ditemukan di sistem PODIUM."
                ], 404);
            }

            $modelName = $plan->Model_Name_Plan ?? null;
            if (!$modelName) {
                return response()->json([
                    'success' => false,
                    'message' => "Model_Name_Plan tidak ditemukan dalam plan."
                ], 500);
            }

            // Log::info("Found plan for model: $modelName");

            // 2. Cari rule di database PODIUM
            $rule = DB::connection('podium')->table('rules')->where('Type_Rule', $modelName)->first();
            if (!$rule) {
                return response()->json([
                    'success' => false,
                    'message' => "Rule untuk model '{$modelName}' tidak ditemukan di sistem PODIUM."
                ], 400);
            }

            // Log::info("Found rule for model: $modelName");

            // 3. Decode Rule_Rule
            $ruleSequenceRaw = $rule->Rule_Rule ?? null;
            if ($ruleSequenceRaw === null) {
                return response()->json([
                    'success' => false,
                    'message' => "Rule_Rule tidak ditemukan dalam rule untuk model '{$modelName}'."
                ], 500);
            }

            $ruleSequence = null;
            if (is_string($ruleSequenceRaw)) {
                $decoded = json_decode($ruleSequenceRaw, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => "JSON decode gagal pada Rule_Rule untuk model '{$modelName}': " . json_last_error_msg()
                    ], 500);
                }
                if (!is_array($decoded)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Format Rule_Rule untuk model '{$modelName}' bukan array setelah decode JSON."
                    ], 500);
                }
                $ruleSequence = $decoded;
            }

            if (!is_array($ruleSequence)) {
                return response()->json([
                    'success' => false,
                    'message' => "Format rule untuk model '{$modelName}' bukan array setelah decode JSON."
                ], 500);
            }

            // Log::info("Decoded Rule_Rule: " . print_r($ruleSequence, true));

            // 4. Cari posisi numerik 'chadet'
            $targetProcessName = 'chadet';
            $position = null;
            foreach ($ruleSequence as $indexStr => $process) {
                if ($process === $targetProcessName) {
                    $position = (int)$indexStr;
                    // Log::info("Found target process '$targetProcessName' at numeric index: $position");
                    break;
                }
            }

            if ($position === null) {
                // Log::info("Target process '$targetProcessName' not found in rule. Returning success.");
                return response()->json([
                    'success' => true,
                    'message' => "Proses '{$targetProcessName}' tidak ditemukan dalam rule untuk model '{$modelName}'. Prasyarat dianggap terpenuhi.",
                    'prerequisites_met' => true,
                    'all_prerequisites_done' => true,
                    'missing_processes' => []
                ]);
            }

            // 5. Decode Record_Plan
            $recordPlanRaw = $plan->Record_Plan ?? null;
            $record = [];
            if (is_string($recordPlanRaw) && !empty($recordPlanRaw)) {
                $decodedRecord = json_decode($recordPlanRaw, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => "JSON decode gagal pada Record_Plan untuk plan {$sequenceNoFormatted}: " . json_last_error_msg()
                    ], 500);
                }
                if (!is_array($decodedRecord)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Format Record_Plan untuk plan {$sequenceNoFormatted} bukan array setelah decode JSON."
                    ], 500);
                }
                $record = $decodedRecord;
            }

            // Log::info("Decoded Record_Plan: " . print_r($record, true));

            // 6. Cek prasyarat
            $allPreviousDone = true;
            $missingPrevious = [];
            // Log::info("Checking prerequisites before index: $position");
            for ($i = 1; $i < $position; $i++) {
                $indexStr = (string)$i;
                $prevProcess = $ruleSequence[$indexStr] ?? null;
                if ($prevProcess) {
                    $processStatus = $record[$prevProcess] ?? null;
                    // Log::info("Checking process '$prevProcess' at index $i, status: " . ($processStatus ?? 'NULL'));
                    if ($processStatus === null || $processStatus === "belum" || $processStatus === "") {
                        $allPreviousDone = false;
                        $missingPrevious[] = $prevProcess;
                        // Log::info("Process '$prevProcess' is missing or not done.");
                    }
                } else {
                    // Log::info("No process found at numeric index $i in Rule_Rule.");
                }
            }

            if ($allPreviousDone) {
                // Log::info("All prerequisites met for sequence $sequenceNoFormatted.");
                return response()->json([
                    'success' => true,
                    'message' => "Semua prasyarat untuk proses '{$targetProcessName}' sudah terpenuhi.",
                    'prerequisites_met' => true,
                    'all_prerequisites_done' => true,
                    'missing_processes' => []
                ]);
            } else {
                // Log::info("Prerequisites NOT met for sequence $sequenceNoFormatted. Missing: " . implode(', ', $missingPrevious));
                return response()->json([
                    'success' => false,
                    'message' => "Proses sebelumnya belum selesai: " . implode(', ', $missingPrevious),
                    'prerequisites_met' => false,
                    'all_prerequisites_done' => false,
                    'missing_processes' => $missingPrevious
                ], 400);
            }

        } catch (\PDOException $e) {
            // Log::error('Database error in checkPrerequisites: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            // Log::error('General error in checkPrerequisites: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa prasyarat: ' . $e->getMessage()
            ], 500);
        }
    }

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
    
    public function storeResize(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'No_Produksi'      => 'required|string|max:255',
            'No_Chasis_Kanban' => 'nullable|string|max:255',
            'No_Chasis_Scan'   => 'nullable|string|max:255',
            'Status_Record'    => 'required|string|max:50',
            'Photo_Ng_Path'    => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:10240', // Validasi ukuran awal opsional, misalnya 10MB
        ]);

        $validated['Time'] = now();

        // Kalau ada file foto
        if ($request->hasFile('Photo_Ng_Path')) {
            $uploadedFile = $request->file('Photo_Ng_Path');

            // Baca file sebagai image Intervention
            $image = Image::make($uploadedFile);

            // Set kualitas awal untuk kompresi
            $quality = 80; // Mulai dari 80, bisa disesuaikan

            // Loop untuk menyesuaikan ukuran file
            $compressedImageBinary = null;
            do {
                // Encode gambar ke binary string dengan kualitas tertentu
                $compressedImageBinary = $image->encode('jpg', $quality); // Selalu encode ke jpg untuk ukuran yang lebih kecil
                $sizeInKB = strlen($compressedImageBinary) / 1024; // Ukuran dalam KB

                // Jika ukuran sudah di bawah 1024 KB (1MB), keluar dari loop
                if ($sizeInKB <= 1024) {
                    break;
                }

                // Jika belum, kurangi kualitas dan coba lagi
                $quality -= 5; // Kurangi kualitas sebesar 5

                // Jika kualitas terlalu rendah, hentikan untuk mencegah infinite loop
                if ($quality < 10) {
                    // Log::warning('Image compression stopped due to low quality threshold reached for No_Produksi: ' . $validated['No_Produksi']);
                    break;
                }
            } while ($sizeInKB > 1024);

            // Simpan file yang sudah dikompres ke dalam storage
            // Generate nama file unik
            $fileName = 'ng_photos/' . $validated['No_Produksi'] . '_' . time() . '.jpg'; // Gunakan .jpg
            $disk = 'uploads'; // Disk yang digunakan

            // Simpan binary string ke disk
            Storage::disk($disk)->put($fileName, $compressedImageBinary);

            // Simpan path ke dalam validated data
            $validated['Photo_Ng_Path'] = $fileName;
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
