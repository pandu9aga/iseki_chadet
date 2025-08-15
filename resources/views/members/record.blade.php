@extends('layouts.member')

@section('style')
<!-- DataTables CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}" />

<style>
    /* Header dan isi tabel rata tengah vertikal dan horizontal */

    .btn.glass {
        min-width: 90px;
        padding-left: 0.8rem;
        padding-right: 0.8rem;
        padding-top: 3px;
        padding-bottom: 3px;
        text-align: center;
        white-space: nowrap;
        color: #fff !important;
        font-size: 14px !important;
        /* tulisan putih */
    }

    /* Jika mau tombol kecil */
    .btn-sm.glass {
        min-width: 80px;
        padding-left: 0.6rem;
        padding-right: 0.6rem;
    }

    /* Tombol Add User biasanya lebih besar, bisa diberi class khusus */
    .btn-add {
        min-width: 110px;
        padding-left: 1.2rem;
        padding-right: 1.2rem;
    }

    #dataTable thead th,
    #dataTable tbody td {
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    /* Responsive scrollbar horizontal */
    .table-responsive {
        overflow-x: auto;
    }

    .button-container {
        display: flex;
        justify-content: space-between; /* tombol kiri & kanan */
        align-items: center;
        width: 100%;
    }

    /* Overlay */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        inset: 0;
        background-color: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: flex-start;
        padding-top: 80px;
    }

    /* Konten modal transparan */
    .custom-modal-content {
        background: rgba(255, 255, 255, 0.2); /* transparan */
        backdrop-filter: blur(15px); /* efek blur kaca */
        border-radius: 12px;
        width: 400px;
        max-width: 90%;
        overflow: hidden;
        animation: slideDown 0.3s ease forwards;
        transform: translateY(-50px);
        opacity: 0;
        border: 1px solid rgba(255,255,255,0.3);
    }

    /* Header */
    .custom-modal-header {
        background: rgba(220, 53, 69, 0.4); /* merah transparan */
        backdrop-filter: blur(10px);
        color: white;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .custom-modal-header h5 {
        margin: 0;
    }

    .custom-modal-close {
        font-size: 20px;
        cursor: pointer;
    }

    /* Body */
    .custom-modal-body {
        padding: 15px;
        color: white;
    }

    /* Footer */
    .custom-modal-footer {
        padding: 10px 15px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* Tombol */
    .btn-secondary {
        background: rgba(108, 117, 125, 0.6);
        color: white;
        border: none;
        padding: 6px 12px;
        cursor: pointer;
        border-radius: 4px;
        backdrop-filter: blur(5px);
    }

    .btn-danger {
        background: rgba(220, 53, 69, 0.6);
        color: white;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 4px;
        backdrop-filter: blur(5px);
    }

    .btn-secondary:hover {
        background: rgba(108, 117, 125, 0.8);
    }

    .btn-danger:hover {
        background: rgba(220, 53, 69, 0.8);
    }

    /* Animasi */
    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    input[type="date"] {
        background: rgba(255, 255, 255, 0.2); /* transparan putih */
        backdrop-filter: blur(10px); /* efek glass */
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 8px;
        padding: 8px 12px;
        color: white;
        font-size: 14px;
        outline: none;
        transition: all 0.2s ease-in-out;
    }

    /* Placeholder dan ikon kalender jadi putih */
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1); /* membuat ikon jadi putih */
    }

    /* Saat fokus */
    input[type="date"]:focus {
        border-color: rgba(255, 255, 255, 0.7);
        background: rgba(255, 255, 255, 0.3);
    }

</style>
@endsection

@section('content')
<div id="home" class="page active py-4">
    <div class="container">
        <div class="content-wrapper">
            <section class="contact-map-section">
                <div class="contact-map glass p-4 shadow-sm rounded">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2>Data Record</h2>
                    </div>
                    <div>
                        <div>
                            Choose Day
                        </div>
                        <form class="user" action="{{ route('record.submit') }}" method="GET">
                            @csrf
                            <span>
                                <span>
                                    <input name="Day_Record" type="date" value="{{ $date }}" required>
                                </span>
                                <span>
                                    <button class="glass btn btn-sm" type="submit">
                                        Apply
                                    </button>
                                </span>
                            </span>
                        </form>
                    </div>
                    <br>
                    <div class="button-container">
                        <form action="{{ route('record.export') }}" method="GET" target="_blank">
                            <input name="Day_Record_Hidden" type="hidden" value="{{ $date }}">
                            <button class="glass btn btn-sm" type="submit">
                                Download Report
                            </button>
                        </form>

                        <button class="glass btn btn-sm" type="button" onclick="openModal('resetReportModal')">
                            Reset Report
                        </button>
                    </div>
                    <div id="resetReportModal" class="custom-modal">
                        <div class="custom-modal-content">
                            <div class="custom-modal-header">
                                <h5>Reset Confirmation?</h5>
                                <span class="custom-modal-close" onclick="closeModal('resetReportModal')">&times;</span>
                            </div>
                            <div class="custom-modal-body">
                                <p>Are you sure to reset records?</p>
                                <p>This action cannot be returned!</p>
                            </div>
                            <div class="custom-modal-footer">
                                <button class="btn-secondary" onclick="closeModal('resetReportModal')">Cancel</button>
                                <a class="btn-danger" href="{{ route('record.reset') }}">Reset</a>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle text-center" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">No Instruksi</th>
                                    <th style="width: 20%;">No Chasis Cheksheet</th>
                                    <th style="width: 20%;">No Chasis Scan</th>
                                    <th style="width: 20%;">Time Record</th>
                                    <th style="width: 20%;">Status</th>
                                    
                                </tr>
                            </thead>
                            
                            <tbody>
                                 @foreach ($records as $record)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-wrap">{{ $record->No_Produksi }}</td>
                                    <td class="text-wrap">{{ $record->No_Chasis_Kanban }}</td>
                                    <td class="text-wrap">{{ $record->No_Chasis_Scan }}</td>
                                    <td class="text-wrap">{{ $record->Time }}</td>
                                    <td class="text-wrap">{{ $record->Status_Record}}</td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        autoWidth: false,
        columnDefs: [
            { width: "5%", targets: 0 },
            { width: "20%", targets: [1,2,3,4] }
        ],
    });
});

function openModal(id) {
    document.getElementById(id).style.display = "flex";
}

function closeModal(id) {
    document.getElementById(id).style.display = "none";
}

// Tutup modal jika klik di luar box
window.onclick = function(event) {
    const modals = document.querySelectorAll('.custom-modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}
</script>
@endsection