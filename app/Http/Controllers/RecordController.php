<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record; // Jangan lupa import model User
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;


class RecordController extends Controller
{
    public function index()
    {
        $page = 'record';

        $date = Carbon::today();
        $records = Record::whereDate('Time', $date)->with('user')->get();

        $date = Carbon::parse($date)->isoFormat('YYYY-MM-DD');

        return view('members.record', compact('page', 'records', 'date'));
    }

    public function submit(Request $request){
        $page = 'record';

        $date = $request->input('Day_Record');
        $records = Record::whereDate('Time', $date)->with('user')->get();

        $date = Carbon::parse($date)->isoFormat('YYYY-MM-DD');

        return view('members.record', compact('page', 'records', 'date'));
    }

    public function export(Request $request) {
        $date = $request->input('Day_Record_Hidden');
        $date = Carbon::parse($date)->format('Y-m-d H:i:s');
        $records = Record::whereDate('Time', $date)->with('user')->get();

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom (tambahkan Approved By)
        $headers = ['No', 'No Instruksi', 'No Chasis Cheksheet', 'No Chasis Scan', 'Time Record', 'Status', 'Approved By'];
        $sheet->fromArray([$headers], NULL, 'A1');

        // Style header (tebal & background abu-abu)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F4F4F']]
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Isi data
        $row = 2;
        foreach ($records as $index => $record) {
            // Ganti NG-Approved jadi NG-OK
            $statusText = $record->Status_Record === 'NG-Approved' ? 'NG-OK' : $record->Status_Record;

            // Ambil nama user, kalau null kasih ""
            $approvedBy = $record->user->Name_User ?? '';

            // Tambahkan data ke Excel
            $sheet->fromArray([
                $index + 1,
                $record->No_Produksi,
                $record->No_Chasis_Kanban,
                $record->No_Chasis_Scan,
                Carbon::parse($record->Time)->format('d-m-Y H:i:s'),
                $statusText,
                $approvedBy
            ], NULL, 'A' . $row);

            // Set warna dan tebal untuk status
            $correctnessCell = 'F' . $row;
            if ($record->Status_Record === 'OK') {
                $sheet->getStyle($correctnessCell)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '008000']] // Hijau
                ]);
            } elseif ($record->Status_Record === 'NG-Approved') {
                $sheet->getStyle($correctnessCell)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'de7e00']] // Oranye
                ]);
            } else {
                $sheet->getStyle($correctnessCell)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FF0000']] // Merah
                ]);
            }

            $row++;
        }

        $date = Carbon::parse($date)->format('Y-m-d');

        // Simpan ke file
        $fileName = "Chasis_Detector_Report_" . $date . ".xlsx";
        $writer = new Xlsx($spreadsheet);
        $filePath = public_path('storage/' . $fileName);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function reset(){
        Record::truncate();
        return redirect()->route('record');
    }

    public function approve($Id_Record)
    {
        $record = Record::findOrFail($Id_Record);
        if ($record->Status_Record === "NG") {
            $record->Status_Record = "NG-Approved";
            $record->Id_User = session('login_id');
            $record->save();
        }

        return response()->json([
            "success" => true,
            "message" => "Record approved",
            "data" => $record
        ]);
    }
}

